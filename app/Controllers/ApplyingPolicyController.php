<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;

class ApplyingPolicyController
{

    private static \PDO $db;

    public static function verifyForTeams()
    {
        static::$db = App::db();
        $q1 = 'SELECT u.name FROM users u LEFT JOIN teams_participants tp ON u.ID = tp.ID ';
        $q2 = 'LEFT JOIN competitions_applications ca1 ON ca1.participantID = u.ID ';
        $q3 = 'LEFT JOIN competitions_applications ca2 ON ca2.participantID = tp.teamID ';
        $q4 = 'GROUP BY u.name HAVING COUNT(DISTINCT ca2.competitionID) + COUNT(DISTINCT ca1.competitionID) < 5';
        $query = (string) $q1 . $q2 . $q3 . $q4;
        $st = static::$db->prepare($query);
        $st->execute();

        $result = $st->fetchAll();
        if(count($result) > 0){
            return json_encode($result);
        }
        return false;
    }
    public static function verify()
    {
        static::$db = App::db();

        $q1 = 'SELECT u.name , COUNT(DISTINCT ca2.competitionID) + COUNT(DISTINCT ca1.competitionID) AS applications FROM users u ';
        $q2 = 'LEFT JOIN teams_participants tp ON u.ID = tp.userID ';
        $q3 = 'LEFT JOIN competitions_applications ca1 ON ca1.participantID = u.ID ';
        $q4 = 'LEFT JOIN competitions_applications ca2 ON ca2.participantID = tp.teamID ';
        $q5 = 'WHERE u.name = ? GROUP BY u.name ';

        $query = (string) $q1 . $q2 . $q3 . $q4 . $q5;

        $st = static::$db->prepare($query);
        $st->bindValue(1, $_SESSION["USER"]["name"]);

        $st->execute();
    
        $result = $st->fetchAll();

        if(count($result) > 0 && $result[0]["applications"] < 5){
            return 5 - $result[0]["applications"];
        }
        return false;
    }
}