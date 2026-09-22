<?php

declare (strict_types = 1);

namespace Carlos\TechAcademy3\Repository;

use Carlos\TechAcademy3\Model\Student;
use Carlos\TechAcademy3\Model\StudentUserRelation;
use PDO;

final class StudentRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function create(Student $student): void
    {
        $statement = $this->connection->prepare('INSERT INTO `STUDENT` (`NAME`, `BIRTH_DATE`, `REGISTRATION`, `CLASS_ID`) VALUES (:name, :birth_date, :registration, :class_id)');
        $statement->execute([
            'name'         => $student->getName(),
            'birth_date'   => $student->getBirthDate(),
            'registration' => $student->getRegistration(),
            'class_id'     => $student->getClassId(),
        ]);
        $student->assignId((int) $this->connection->lastInsertId());
    }

    public function findById(int $id): ?Student
    {
        $statement = $this->connection->prepare('SELECT `ID`, `NAME`, `BIRTH_DATE`, `REGISTRATION`, `CLASS_ID` FROM `STUDENT` WHERE `ID` = :id');
        $statement->execute(['id' => $id]);
        return $this->hydrate($statement->fetch(PDO::FETCH_ASSOC));
    }

    public function findAll(): array
    {
        $rows     = $this->connection->query('SELECT `ID`, `NAME`, `BIRTH_DATE`, `REGISTRATION`, `CLASS_ID` FROM `STUDENT` ORDER BY `NAME`')->fetchAll(PDO::FETCH_ASSOC);
        $students = [];

        foreach ($rows as $row) {
            $student = $this->hydrate($row);

            if ($student !== null) {
                $students[] = $student;
            }
        }

        return $students;
    }

    public function findByClassId(int $classId): array
    {
        $statement = $this->connection->prepare('SELECT `ID`, `NAME`, `BIRTH_DATE`, `REGISTRATION`, `CLASS_ID` FROM `STUDENT` WHERE `CLASS_ID` = :class_id ORDER BY `NAME`');
        $statement->execute(['class_id' => $classId]);
        $rows     = $statement->fetchAll(PDO::FETCH_ASSOC);
        $students = [];

        foreach ($rows as $row) {
            $student = $this->hydrate($row);

            if ($student !== null) {
                $students[] = $student;
            }
        }

        return $students;
    }

    public function findByUserId(int $userId): array
    {
        $statement = $this->connection->prepare(
            'SELECT `STUDENT`.`ID`, `STUDENT`.`NAME`, `STUDENT`.`BIRTH_DATE`, `STUDENT`.`REGISTRATION`, `STUDENT`.`CLASS_ID`
             FROM `STUDENT`
             INNER JOIN `STUDENT_has_USER` ON `STUDENT_has_USER`.`STUDENT_ID` = `STUDENT`.`ID`
             WHERE `STUDENT_has_USER`.`USER_ID` = :user_id
             ORDER BY `STUDENT`.`NAME`'
        );
        $statement->execute(['user_id' => $userId]);

        $students = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $student = $this->hydrate($row);

            if ($student !== null) {
                $students[] = $student;
            }
        }

        return $students;
    }

    public function isLinkedToUser(int $studentId, int $userId): bool
    {
        $statement = $this->connection->prepare(
            'SELECT 1 FROM `STUDENT_has_USER` WHERE `STUDENT_ID` = :student_id AND `USER_ID` = :user_id LIMIT 1'
        );
        $statement->execute(['student_id' => $studentId, 'user_id' => $userId]);

        return $statement->fetchColumn() !== false;
    }

    public function findByRegistration(string $registration): ?Student
    {
        $statement = $this->connection->prepare('SELECT `ID`, `NAME`, `BIRTH_DATE`, `REGISTRATION`, `CLASS_ID` FROM `STUDENT` WHERE `REGISTRATION` = :registration');
        $statement->execute(['registration' => trim($registration)]);
        return $this->hydrate($statement->fetch(PDO::FETCH_ASSOC));
    }

    public function update(Student $student): void
    {
        $statement = $this->connection->prepare('UPDATE `STUDENT` SET `NAME` = :name, `BIRTH_DATE` = :birth_date, `REGISTRATION` = :registration, `CLASS_ID` = :class_id WHERE `ID` = :id');
        $statement->execute(['id' => $student->getId(), 'name' => $student->getName(), 'birth_date' => $student->getBirthDate(), 'registration' => $student->getRegistration(), 'class_id' => $student->getClassId()]);
    }

    public function delete(Student $student): void
    {
        $statement = $this->connection->prepare('DELETE FROM `STUDENT` WHERE `ID` = :id');
        $statement->execute(['id' => $student->getId()]);
    }

    public function saveUserRelation(StudentUserRelation $relation): void
    {
        $parameters = [
            'student_id'   => $relation->getStudentId(),
            'user_id'      => $relation->getUserId(),
            'relationship' => $relation->getRelationship(),
        ];
        $findStatement = $this->connection->prepare('SELECT `STUDENT_ID` FROM `STUDENT_has_USER` WHERE `STUDENT_ID` = :student_id AND `USER_ID` = :user_id');
        $findStatement->execute([
            'student_id' => $relation->getStudentId(),
            'user_id'    => $relation->getUserId(),
        ]);

        if ($findStatement->fetch(PDO::FETCH_ASSOC) !== false) {
            $statement = $this->connection->prepare('UPDATE `STUDENT_has_USER` SET `RELATIONSHIP` = :relationship WHERE `STUDENT_ID` = :student_id AND `USER_ID` = :user_id');
        } else {
            $statement = $this->connection->prepare('INSERT INTO `STUDENT_has_USER` (`STUDENT_ID`, `USER_ID`, `RELATIONSHIP`) VALUES (:student_id, :user_id, :relationship)');
        }

        $statement->execute($parameters);
    }

    public function removeUserRelation(int $studentId, int $userId): void
    {
        $statement = $this->connection->prepare('DELETE FROM `STUDENT_has_USER` WHERE `STUDENT_ID` = :student_id AND `USER_ID` = :user_id');
        $statement->execute(['student_id' => $studentId, 'user_id' => $userId]);
    }

    private function hydrate(array | false $row): ?Student
    {
        if ($row === false) {
            return null;
        }

        return Student::fromDatabase((int) $row['ID'], $row['NAME'], $row['BIRTH_DATE'], $row['REGISTRATION'], $row['CLASS_ID'] === null ? null : (int) $row['CLASS_ID']);
    }
}
