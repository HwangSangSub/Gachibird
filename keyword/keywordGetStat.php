<?
    include "../lib/common.php";

    $DB_con = db1();
    $key_idx = trim($key_idx);

    $query = "SELECT * FROM keyword_stat A INNER JOIN keyword B ON A.key_idx = B.idx WHERE key_idx = :key_idx ORDER BY year, month ASC ";
    $stmt = $DB_con->prepare($query);
    $stmt->bindParam(":key_idx", $key_idx);
    $stmt->execute();
    $rowCount = $stmt->rowCount();

    if ($rowCount > 0) {
        $stat = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            $keyword = $row['name'];
            $year = $row['year'];
            $month = $row['month'];
            $total_cnt = $row['total_cnt'];
            $pc_cnt = $row['pc_cnt'];
            $mobile_cnt = $row['mobile_cnt'];
            $total_ave_cnt = $row['total_ave_cnt'];
            $pc_ave_cnt = $row['pc_ave_cnt'];
            $mobile_ave_cnt = $row['mobile_ave_cnt'];
            $pc_ave_rate = $row['pc_ave_rate'];
            $mobile_ave_rate = $row['mobile_ave_rate'];
            $pl_avg_depth = $row['pl_avg_depth'];
            $competition = $row['competition'];
            
            $a = [
                "keyword" => $keyword,
                "year" => $year,
                "month" => $month,
                "total_cnt" => $total_cnt,
                "pc_cnt" => $pc_cnt,
                "mobile_cnt" => $mobile_cnt,
                "total_ave_cnt" => $total_ave_cnt,
                "pc_ave_rate" => $pc_ave_rate,
                "mobile_ave_cnt" => $mobile_ave_cnt,
                "pc_ave_rate" => $pc_ave_rate,
                "mobile_ave_rate" => $mobile_ave_rate,
                "pl_avg_depth" => $pl_avg_depth,
                "competition" => $competition,
            ];
    
            array_push($stat, $a);
        }
        print_r($stat);
    } else {
        print_r(["msg" => "실패"]);
    }
    

?>