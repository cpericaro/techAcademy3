<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Repository;

use Carlos\TechAcademy3\Model\ClassSchedule;
use Carlos\TechAcademy3\Model\SchoolClass;
use PDO;

final class ClassRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function create(SchoolClass $class): void
    {
        $statement = $this->connection->prepare('INSERT INTO `CLASS` (`NAME`, `YEAR`) VALUES (:name, :year)');
        $statement->execute(['name' => $class->getName(), 'year' => $class->getYear()]);
        $class->assignId((int) $this->connection->lastInsertId());
    }

    public function findById(int $id): ?SchoolClass
    {
        $statement = $this->connection->prepare('SELECT `ID`, `NAME`, `YEAR` FROM `CLASS` WHERE `ID` = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : SchoolClass::fromDatabase((int) $row['ID'], $row['NAME'], (int) $row['YEAR']);
    }

    public function findAll(): array
    {
        $rows = $this->connection->query('SELECT `ID`, `NAME`, `YEAR` FROM `CLASS` ORDER BY `YEAR` DESC, `NAME`')->fetchAll(PDO::FETCH_ASSOC);
        $classes = [];

        foreach ($rows as $row) {
            $classes[] = SchoolClass::fromDatabase((int) $row['ID'], $row['NAME'], (int) $row['YEAR']);
        }

        return $classes;
    }

    public function update(SchoolClass $class): void
    {
        $statement = $this->connection->prepare('UPDATE `CLASS` SET `NAME` = :name, `YEAR` = :year WHERE `ID` = :id');
        $statement->execute(['id' => $class->getId(), 'name' => $class->getName(), 'year' => $class->getYear()]);
    }

    public function delete(SchoolClass $class): void
    {
        $students = $this->connection->prepare('UPDATE `STUDENT` SET `CLASS_ID` = NULL WHERE `CLASS_ID` = :class_id');
        $students->execute(['class_id' => $class->getId()]);
        $schedules = $this->connection->prepare('DELETE FROM `CLASS_SCHEDULE` WHERE `CLASS_ID` = :class_id');
        $schedules->execute(['class_id' => $class->getId()]);
        $statement = $this->connection->prepare('DELETE FROM `CLASS` WHERE `ID` = :id');
        $statement->execute(['id' => $class->getId()]);
    }

    public function hasLessons(int $classId): bool
    {
        $statement = $this->connection->prepare('SELECT 1 FROM `LESSON` WHERE `CLASS_ID` = :class_id LIMIT 1');
        $statement->execute(['class_id' => $classId]);
        return $statement->fetchColumn() !== false;
    }

    public function createSchedule(ClassSchedule $schedule): void
    {
        $statement = $this->connection->prepare('INSERT INTO `CLASS_SCHEDULE` (`WEEKDAY`, `START_TIME`, `END_TIME`, `CLASS_ID`) VALUES (:weekday, :start_time, :end_time, :class_id)');
        $statement->execute(['weekday' => $schedule->getWeekday(), 'start_time' => $schedule->getStartTime(), 'end_time' => $schedule->getEndTime(), 'class_id' => $schedule->getClassId()]);
        $schedule->assignId((int) $this->connection->lastInsertId());
    }

    public function findSchedulesByClassId(int $classId): array
    {
        $statement = $this->connection->prepare('SELECT `ID`, `WEEKDAY`, `START_TIME`, `END_TIME`, `CLASS_ID` FROM `CLASS_SCHEDULE` WHERE `CLASS_ID` = :class_id ORDER BY `WEEKDAY`, `START_TIME`');
        $statement->execute(['class_id' => $classId]);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $schedules = [];

        foreach ($rows as $row) {
            $schedules[] = ClassSchedule::fromDatabase((int) $row['ID'], (int) $row['WEEKDAY'], $row['START_TIME'], $row['END_TIME'], (int) $row['CLASS_ID']);
        }

        return $schedules;
    }
    public function deleteSchedule(int $id, int $classId): void
    {
        $statement = $this->connection->prepare('DELETE FROM `CLASS_SCHEDULE` WHERE `ID` = :id AND `CLASS_ID` = :class_id');
        $statement->execute(['id' => $id, 'class_id' => $classId]);
    }
}
