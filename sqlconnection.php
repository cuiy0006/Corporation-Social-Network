<?php

class sql
{
    private $servername = "localhost";
    private $username = "root";
    private $password = "******";
    private $dbname = "proj";
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if($this->conn->connect_error)
        {
            echo "not succeed";
        }
    }

    public function closeConn()
    {
        $this->conn->close();
    }

    public function getUserInfo($EID, $password)
    {
        $query = "SELECT * from Employee where EID ='" . $EID . "' and password = '" . $password . "'";
        $res = $this->conn->query($query);
        return $res;
    }

    public function getUserInfoByEID($EID)
    {
        $query = "SELECT Name, Title, Email from Employee where EID ='" . $EID . "'";
        $res = $this->conn->query($query);
        return $res;
    }

    public function getUserInfoByTID($TID)
    {
        $stmt = $this->conn->prepare("SELECT employee.EID, employee.Name, employee.Title, employee.Email from team_employee, employee where TID = ? and team_employee.EID = employee.EID");
        $stmt->bind_param("s", $TID);
        $stmt->execute();
        return $stmt;
    }

    public function getUserInfoByNotInTeam($TID, $Name)
    {
        $stmt = $this->conn->prepare("SELECT employee.EID, employee.Name, employee.Title, employee.Email from employee 
        where employee.Name like ? and employee.EID not in (SELECT EID from team_employee where TID = ?)");
        $a = $this->conn->error;
        $Name = '%'.$Name.'%';
        $stmt->bind_param("ss", $Name, $TID);
        $stmt->execute();
        return $stmt;
    }

    public function UpdateUserInfo($EID, $password, $email)
    {
        $query = "UPDATE employee set email = '". $email ."', password = '". $password ."' where EID = '". $EID. "'";
        $res = $this->conn->query($query);
        return $res;
    }


    public function getPriviledge()
    {
        $query = "SELECT * from priviledge";
        $res = $this->conn->query($query);
        return $res;
    }

    public function getTeamInfoByEID($EID, $TName)
    {
        $query = "SELECT team.TID, team.Name, Description, employee.Name as ManagerName, team.ManagerID, StartTime from team, team_employee, employee 
                 where team.ManagerID = employee.EID and team.TID = team_employee.TID and team_employee.EID = '".$EID."' and team.Name like '%".$TName."%'
                 order by StartTime desc";

        $res = $this->conn->query($query);
        return $res;
    }

    public function getTeamInfoByTID($TID)
    {
        $query = "SELECT * from team where TID = '".$TID."'";
        $res = $this->conn->query($query);
        return $res;
    }

    public function ExistTeamID($TID)
    {
        $query = "SELECT * from team where TID = '".$TID."'";
        $res = $this->conn->query($query);
        if($row = $res->fetch_assoc())
            return TRUE;
        else
            return FALSE;
    }

    public function InsertTeamInfo($TID, $Name, $Description, $ManagerID, $StartTime)
    {

        $query1 = "INSERT into team(TID, Name, Description, ManagerID, StartTime) values('".$TID."', '".$Name."', '".$Description."', '".$ManagerID."', '".$StartTime."');";
        $query2 = "INSERT into team_employee(TID, EID, JoinTime) values('".$TID."', '".$ManagerID."', '".$StartTime."');";
        $query = $query1.$query2;
        $res = mysqli_multi_query($this->conn, $query);
        return $res;
    }

    public function InsertEIDintoTeam($TID,$EID, $JoinTime)
    {
        $stmt = $this->conn->prepare("INSERT INTO team_employee(TID, EID, JoinTime) values(?,?,?)");
        $stmt->bind_param("sss", $TID, $EID, $JoinTime);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function RemoveEIDfromTeam($EID, $TID)
    {
        $stmt = $this->conn->prepare("DELETE from team_employee WHERE EID = ? and TID = ?");
        $stmt->bind_param("ss", $EID, $TID);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function UpdateManagerOfTeam($TID, $EID)
    {
        $stmt = $this->conn->prepare("Update team set ManagerID = ? WHERE TID = ?");
        $stmt->bind_param("ss", $EID, $TID);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function UpdateTeamInfo($TID, $Name, $Description)
    {
        $stmt = $this->conn->prepare("UPDATE team SET Name = ?, Description = ? where TID = ?");  
        $stmt->bind_param("sss", $Name, $Description, $TID);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    
    public function getProjectInfoByEID($EID, $PName)
    {
        $stmt = $this->conn->prepare("SELECT PID, Project.Name, Description, employee.Name as ManagerName, ManagerID, StartTime, EndTime from project, employee
        where project.ManagerID = employee.EID and project.Name like ? 
        and (PID in (SELECT PID from project_team where TID in (SELECT TID from team_employee where EID = ?)) or project.ManagerID = ?)
        order by StartTime DESC");
        $a = $this->conn->error;
        $PName = '%'.$PName.'%';
        $stmt->bind_param("sss", $PName, $EID, $EID);
        $stmt->execute();
        return $stmt;

    }

    public function ExistProjectID($PID)
    {
        $stmt = $this->conn->prepare("SELECT * from project where PID = ?");
        $a = $this->conn->error;
        $stmt->bind_param("s", $PID);
        $stmt->execute();
        if($row = $stmt->fetch())
            return TRUE;
        else
            return FALSE;
    } 

    public function InsertProjectInfo($PID, $Name, $Description, $ManagerID, $StartTime)
    {
        $stmt = $this->conn->prepare("INSERT INTO project(PID, Name, Description, ManagerID, StartTime) values(?,?,?,?,?)");
        $stmt->bind_param("sssss", $PID, $Name, $Description, $ManagerID, $StartTime);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }


    public function closeProject($PID, $EndTime)
    {
        $stmt = $this->conn->prepare("UPDATE project SET EndTime = ? where PID = ?");  
        $stmt->bind_param("ss", $EndTime, $PID);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function getProjectInfoByPID($PID)
    {
        $query = "SELECT * from project where PID = '".$PID."'";
        $res = $this->conn->query($query);
        return $res;
    }

    public function UpdateProjectInfo($PID, $Name, $Description)
    {
        $stmt = $this->conn->prepare("UPDATE project SET Name = ?, Description = ? where PID = ?");  
        $stmt->bind_param("sss", $Name, $Description, $PID);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function getTeamInfoByPID($PID)
    {
        $stmt = $this->conn->prepare("SELECT team.TID, Name, Description, ManagerID, StartTime from project_team, team 
        where PID = ? and project_team.TID = team.TID");
        $a = $this->conn->error;
        $stmt->bind_param("s", $PID);
        $stmt->execute();
        return $stmt;
    }

    public function getTeamInfoByNotInProject($PID, $Name)
    {
        $stmt = $this->conn->prepare("SELECT team.TID, Name, Description, ManagerID, StartTime from team 
        where team.Name like ? and team.TID not in (SELECT TID from project_team where PID = ?)");
        $a = $this->conn->error;
        $Name = '%'.$Name.'%';
        $stmt->bind_param("ss", $Name, $PID);
        $stmt->execute();
        return $stmt;
    }


    public function RemoveTIDfromProject($TID, $PID)
    {
        $stmt = $this->conn->prepare("DELETE from project_team WHERE TID = ? and PID = ?");
        $stmt->bind_param("ss", $TID, $PID);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }


    public function InsertTIDintoProject($PID,$TID, $JoinTime)
    {
        $stmt = $this->conn->prepare("INSERT INTO project_team(PID, TID, JoinTime) values(?,?,?)");
        $stmt->bind_param("sss", $PID, $TID, $JoinTime);
        $a = $this->conn->error;
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    
    }

    public function getProjectInfoByTID($TID, $PName)
    {
        $stmt = $this->conn->prepare("SELECT PID, Project.Name, Description, employee.Name as ManagerName, ManagerID, StartTime, EndTime from project, employee
        where project.ManagerID = employee.EID and project.Name like ?
        and PID in (SELECT PID from project_team where TID = ?)
        order by StartTime DESC");
        $a = $this->conn->error;
        $PName = '%'.$PName.'%';
        $stmt->bind_param("ss",$PName, $TID);
        $stmt->execute();
        return $stmt;

    }

    public function insertUpdate($PID, $EID, $updateTime, $location, $Description)
    {
        $stmt = $this->conn->prepare("INSERT INTO updates(PID, EID, UpdateTime, Description, Location) values(?,?,?,?,?)");
        $stmt->bind_param("sssss", $PID, $EID, $updateTime, $Description, $location);
        $a = $this->conn->error;
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function insertResources($PID, $EID, $updateTime, $Rtype, $Content)
    {
        $stmt = $this->conn->prepare("INSERT INTO resources(PID, EID, updateTime, RType, Content) values(?,?,?,?,?)");
        $stmt->bind_param("sssss", $PID, $EID, $updateTime, $Rtype, $Content);
        $a = $this->conn->error;
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function getResources($PID, $EID, $updateTime, $Rtype)
    {
        $stmt = $this->conn->prepare("SELECT Content from resources 
        where PID = ? and EID = ? and UpdateTime = ? and RType = ?");
        $a = $this->conn->error;
        $stmt->bind_param("ssss", $PID, $EID, $updateTime, $Rtype);
        $a = $this->conn->error;
        $res = $stmt->execute();
        return $stmt;
    }


    public function getUpdateByEID($EID, $PName)
    {
        $stmt = $this->conn->prepare("SELECT project.PID, project.Name, employee.EID, employee.Name, UpdateTime, updates.Description, Location from updates, project, employee 
        where project.Name like ? and project.PID = updates.PID and employee.EID = updates.EID
        and project.PID in (select PID from project_team where TID in (select TID from team_employee where EID = ?))
        order by UpdateTime desc");
        $PName = '%'.$PName.'%';
        $a = $this->conn->error;
        $stmt->bind_param("ss", $PName, $EID);
        $a = $this->conn->error;
        $stmt->execute();
        $a = $this->conn->error;
        return $stmt;

    }

    public function insertComment($PID, $EID, $updateTime, $Content, $postTime, $posterID)
    {
        $stmt = $this->conn->prepare("INSERT INTO comments(PID, EID, updateTime, Content, PostTime, PosterID) values(?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $PID, $EID, $updateTime, $Content, $postTime, $posterID);
        $res = $stmt->execute();
        $a = $this->conn->error;
        $stmt->close();
        return $res;
    }

    public function getComments($PID, $EID, $updateTime)
    {
        $stmt = $this->conn->prepare("SELECT Content, PosterID, employee.Name, PostTime from comments,employee 
        where PID = ? and comments.EID = ? and UpdateTime = ? and employee.EID = PosterID 
        order by PostTime desc");
        $a = $this->conn->error;
        $stmt->bind_param("sss", $PID, $EID, $updateTime);
        $a = $this->conn->error;
        $res = $stmt->execute();
        return $stmt;
    }

    public function getData()
    {
        $stmt = $this->conn->prepare("SELECT RType, Content from Resources");
        $a = $this->conn->error;
        $stmt->execute();
        $a = $this->conn->error;
        return $stmt;
    }
}
?>
