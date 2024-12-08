<?

include '../lib/common.php';

$DB_con = db1();

if ((int)date('d') == 1) { // 달의 첫번쨰날이면 저번달 정보 저장
    $year = date("Y");
    $month = date("m")-1;
    $query = "INSERT INTO keyword_stat SELECT NULL AS idx, key_idx, '{$year}' AS year, '{$month}' AS month, total_cnt, pc_cnt, mobile_cnt, total_ave_cnt, pc_ave_cnt, mobile_ave_cnt, pc_ave_rate, mobile_ave_rate, pl_avg_depth, competition FROM keyword_stat_now";
    $stmt = $DB_con->prepare($query);
    $stmt->execute();
    $row_count = $stmt->rowCount();

    echo "success";
} else {
    echo "fail";
}