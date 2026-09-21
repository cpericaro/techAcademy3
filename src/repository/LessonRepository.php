<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Repository;

use Carlos\TechAcademy3\Model\Attendance;
use Carlos\TechAcademy3\Model\Enum\AttendanceStatus;
use Carlos\TechAcademy3\Model\Lesson;
use PDO;

final class LessonRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function create(Lesson $lesson): void
    {
        $statement = $this->connection->prepare('INSERT INTO `LESSON` (`LESSON_DATE`, `PLANNED_CONTENT`, `CONTENT`, `STATUS`, `CLASS_ID`) VALUES (:lesson_date, :planned_content, :content, :status, :class_id)');
        $statement->execute([
            'lesson_date' => $lesson->getLessonDate(),
            'planned_content' => $lesson->getPlannedContent(),
            'content' => $lesson->getContent(),
            'status' => $lesson->getStatus(),
            'class_id' => $lesson->getClassId(),
        ]);
        $lesson->assignId((int) $this->connection->lastInsertId());
    }

    public function findById(int $id): ?Lesson
    {
        $statement = $this->connection->prepare('SELECT `ID`, `LESSON_DATE`, `PLANNED_CONTENT`, `CONTENT`, `STATUS`, `CLASS_ID` FROM `LESSON` WHERE `ID` = :id');
        $statement->execute(['id' => $id]);
        return $this->hydrateLesson($statement->fetch(PDO::FETCH_ASSOC));
    }

    public function findAll(): array
    {
        $rows = $this->connection->query('SELECT `ID`, `LESSON_DATE`, `PLANNED_CONTENT`, `CONTENT`, `STATUS`, `CLASS_ID` FROM `LESSON` ORDER BY `LESSON_DATE` DESC')->fetchAll(PDO::FETCH_ASSOC);
        $lessons = [];

        foreach ($rows as $row) {
            $lesson = $this->hydrateLesson($row);

            if ($lesson !== null) {
                $lessons[] = $lesson;
            }
        }

        return $lessons;
    }

    public function update(Lesson $lesson): void
    {
        $statement = $this->connection->prepare('UPDATE `LESSON` SET `LESSON_DATE` = :lesson_date, `PLANNED_CONTENT` = :planned_content, `CONTENT` = :content, `STATUS` = :status WHERE `ID` = :id');
        $statement->execute(['id' => $lesson->getId(), 'lesson_date' => $lesson->getLessonDate(), 'planned_content' => $lesson->getPlannedContent(), 'content' => $lesson->getContent(), 'status' => $lesson->getStatus()]);
    }

    public function delete(Lesson $lesson): void
    {
        $attendance = $this->connection->prepare('DELETE FROM `ATTENDANCE` WHERE `LESSON_ID` = :lesson_id');
        $attendance->execute(['lesson_id' => $lesson->getId()]);
        $statement = $this->connection->prepare('DELETE FROM `LESSON` WHERE `ID` = :id');
        $statement->execute(['id' => $lesson->getId()]);
    }

    public function saveAttendance(Attendance $attendance): void
    {
        $parameters = [
            'status' => $attendance->getStatus()->value,
            'student_id' => $attendance->getStudentId(),
            'lesson_id' => $attendance->getLessonId(),
        ];
        $findStatement = $this->connection->prepare('SELECT `ID` FROM `ATTENDANCE` WHERE `STUDENT_ID` = :student_id AND `LESSON_ID` = :lesson_id');
        $findStatement->execute([
            'student_id' => $attendance->getStudentId(),
            'lesson_id' => $attendance->getLessonId(),
        ]);

        if ($findStatement->fetch(PDO::FETCH_ASSOC) !== false) {
            $statement = $this->connection->prepare('UPDATE `ATTENDANCE` SET `STATUS` = :status WHERE `STUDENT_ID` = :student_id AND `LESSON_ID` = :lesson_id');
        } else {
            $statement = $this->connection->prepare('INSERT INTO `ATTENDANCE` (`STATUS`, `STUDENT_ID`, `LESSON_ID`) VALUES (:status, :student_id, :lesson_id)');
        }

        $statement->execute($parameters);
    }

    public function findAttendanceByLessonId(int $lessonId): array
    {
        $statement = $this->connection->prepare('SELECT `ID`, `STATUS`, `STUDENT_ID`, `LESSON_ID` FROM `ATTENDANCE` WHERE `LESSON_ID` = :lesson_id ORDER BY `STUDENT_ID`');
        $statement->execute(['lesson_id' => $lessonId]);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $attendanceList = [];

        foreach ($rows as $row) {
            $attendanceList[] = Attendance::fromDatabase((int) $row['ID'], AttendanceStatus::from((int) $row['STATUS']), (int) $row['STUDENT_ID'], (int) $row['LESSON_ID']);
        }

        return $attendanceList;
    }

    public function findAttendanceByStudentId(int $studentId): array
    {
        $statement = $this->connection->prepare('SELECT `ID`, `STATUS`, `STUDENT_ID`, `LESSON_ID` FROM `ATTENDANCE` WHERE `STUDENT_ID` = :student_id ORDER BY `LESSON_ID` DESC');
        $statement->execute(['student_id' => $studentId]);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $attendanceList = [];

        foreach ($rows as $row) {
            $attendanceList[] = Attendance::fromDatabase((int) $row['ID'], AttendanceStatus::from((int) $row['STATUS']), (int) $row['STUDENT_ID'], (int) $row['LESSON_ID']);
        }

        return $attendanceList;
    }
    private function hydrateLesson(array|false $row): ?Lesson
    {
        if ($row === false) {
            return null;
        }

        return Lesson::fromDatabase((int) $row['ID'], $row['LESSON_DATE'], $row['PLANNED_CONTENT'], $row['CONTENT'], (int) $row['STATUS'], (int) $row['CLASS_ID']);
    }
}
