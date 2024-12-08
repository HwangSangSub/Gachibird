<?
	$menu = "4";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;
	

	$sql_search=" WHERE 1=1 ";

	if ($fr_date != "" || $to_date != "" ) {
		//$sql_search.=" AND (reg_Date between ':fr_date' AND ':to_date')";
		$sql_search.=" AND (DATE_FORMAT(date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(date,'%Y-%m-%d') <= :to_date)";
	}
	
	$DB_con = db1();

    //전체 카운트
	$cntQuery = "SELECT SUM(make_keyword_date) AS make_keyword_date, SUM(add_keyword_date) AS add_keyword_date, SUM(cancel_keyword_date) AS cancel_keyword_date, SUM(add_bookmark_date) AS add_bookmark_date, SUM(cancel_bookmark_date) AS cancel_bookmark_date FROM keyword_state {$sql_search}";

    $cntStmt = $DB_con->prepare($cntQuery);
    
	if ($fr_date != "" || $to_date != "" ) {
        $cntStmt->bindValue(":fr_date",$fr_date);
	    $cntStmt->bindValue(":to_date",$to_date);
	}
    
    $cntStmt->execute();

	while($row = $cntStmt->fetch()) {
		$make_keyword_date = $row['make_keyword_date'];
		$add_keyword_date = $row['add_keyword_date'];
		$cancel_keyword_date = $row['cancel_keyword_date'];
		$add_bookmark_date = $row['add_bookmark_date'];
		$cancel_bookmark_date = $row['cancel_bookmark_date'];
	}

	$rows = 10;
	$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
	if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
	$from_record = ($page - 1) * $rows; // 시작 열을 구함

	$sql_group = "GROUP BY left(date,10)";
	$sql_order = "ORDER BY left(date,10) DESC";

	//목록
	$query = "SELECT * from keyword_state";
	$query .= " {$sql_search} {$sql_group} {$sql_order} limit {$from_record}, {$rows} ";
	//echo $query."<BR>";
	//exit;

	$stmt = $DB_con->prepare($query);

	if ($fr_date != "" || $to_date != "" ) {
	    $stmt->bindValue(":fr_date",$fr_date);
	    $stmt->bindValue(":to_date",$to_date);
	}


	if($findword != "")  {
	    $stmt->bindValue(':findword','%'.trim($findword).'%');
	}
	
	$stmt->execute();
	$numCnt = $stmt->rowCount();

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findOs=".urlencode($findOs)."&amp;findword=".urlencode($findword);
	
	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script type="text/javascript" src="<?=UDEV_DIR?>/keywords/js/keyword.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">키워드 통계</h1>
		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">기간 내 등록 키워드 수 </span><span class="ov_num"><?=number_format($add_keyword_date);?>명 </span>&nbsp;
			<span class="btn_ov01"><span class="ov_txt">기간 내 해제한 키워드 수 </span><span class="ov_num"><?=number_format($cancel_keyword_date);?>명 </span>&nbsp;
			<span class="btn_ov01"><span class="ov_txt">기간 내 추가한 뉴스 북마크 수 </span><span class="ov_num"><?=number_format($add_bookmark_date);?>명 </span>&nbsp;
			<span class="btn_ov01"><span class="ov_txt">기간 내 해제한 뉴스 북마크 수 </span><span class="ov_num"><?=number_format($cancel_bookmark_date);?>명 </span>&nbsp;
		</div>
		<form class="local_sch03 local_sch"  autocomplete="off">
        <div class="sch_last">
            <strong>검색일자</strong>
            <input type="text" id="fr_date"  name="fr_date" value="<?=$fr_date?>" class="frm_input" size="10" maxlength="10"> ~
            <input type="text" id="to_date"  name="to_date" value="<?=$to_date?>" class="frm_input" size="10" maxlength="10">
            <button type="button" onclick="javascript:set_date('오늘');">오늘</button>
            <button type="button" onclick="javascript:set_date('어제');">어제</button>
            <button type="button" onclick="javascript:set_date('이번주');">이번주</button>
            <button type="button" onclick="javascript:set_date('이번달');">이번달</button>
            <button type="button" onclick="javascript:set_date('지난주');">지난주</button>
            <button type="button" onclick="javascript:set_date('지난달');">지난달</button>
            <button type="button" onclick="javascript:set_date('전체');">전체</button>
            <input type="submit" value="검색" class="btn_submit">
        	<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
        </div>
		</form>



<div class="local_desc01 local_desc">
    <p>
        기간선택이 없을 경우 기본적으로 전체 일자의 데이터를 통계처리합니다.<br>
		해당 기간의 일별 데이터는 아래에 테이블로 표시되며 해당기간의 총합은 검색일자 위에 표시됩니다.<br>
		일자는 최근일자부터 역순으로 정렬됩니다.
    </p>
</div>

<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>키워드 관리 목록</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
		<th scope="col" id="mb_list_idx">순번</th>
        <th scope="col" id="mb_list_data">일자</th>
        <th scope="col" id="mb_list_ncnt">최초 등록한 키워드 수</th>
        <th scope="col" id="mb_list_ncnt">회원이 등록한 키워드 수</th>
        <th scope="col" id="mb_list_ycnt">회원이 해제한 키워드 수</th>
		<th scope="col" id="mb_list_dcnt">회원이 등록한 뉴스 북마크 수</th>		
		<th scope="col" id="mb_list_dcnt">회원이 해제한 뉴스 북마크 수</th>		
    </tr>
    </thead>
    <tbody>

    <?

	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row = $stmt->fetch()) {
       // $bg = 'bg'.($stmt->fetch()%2);

		$from_record++;
		$date = $row['date'];
		$mkd = $row['make_keyword_date'];
		$akd = $row['add_keyword_date'];
		$ckd = $row['cancel_keyword_date'];
		$abd = $row['add_bookmark_date'];
		$cbd = $row['cancel_bookmark_date'];
    ?>

    <tr class="<?=$bg?>">
        <td headers="mb_list_idx" class="td_idx"><?=$from_record?></td>
        <td headers="mb_list_date" class="td_date"><?=$date?></td>
        <td headers="mb_list_ncnt"><?=$mkd?></td>
        <td headers="mb_list_ncnt"><?=$akd?></td>
		<td headers="mb_list_ycnt"><?=$ckd?></td>
		<td headers="mb_list_dcnt"><?=$abd?></td>
		<td headers="mb_list_dcnt"><?=$cbd?></td>
    </tr>
    <? 

		}
	?>   
	<? } else { ?>
	<tr>
		<td colspan="13" class="empty_table">자료가 없습니다.</td>
	</tr>
	<? } ?>
        </tbody>
    </table>
</div>

</form>
<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>


<script>
	$(function(){
		$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true });
	});


	function set_date(today)
	{
		<?
			 $date_term = date('w', SERVER_TIME);
			 $week_term = $date_term + 7;
			 $last_term = strtotime(date('Y-m-01', SERVER_TIME));
		?>
		if (today == "오늘") {
			document.getElementById("fr_date").value = "<?php echo TIME_YMD; ?>";
			document.getElementById("to_date").value = "<?php echo TIME_YMD; ?>";
		} else if (today == "어제") {
			document.getElementById("fr_date").value = "<?php echo date('Y-m-d', SERVER_TIME - 86400); ?>";
			document.getElementById("to_date").value = "<?php echo date('Y-m-d', SERVER_TIME - 86400); ?>";
		} else if (today == "이번주") {
			document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$date_term.' days', SERVER_TIME)); ?>";
			document.getElementById("to_date").value = "<?php echo date('Y-m-d', SERVER_TIME); ?>";
		} else if (today == "이번달") {
			document.getElementById("fr_date").value = "<?php echo date('Y-m-01', SERVER_TIME); ?>";
			document.getElementById("to_date").value = "<?php echo date('Y-m-d', SERVER_TIME); ?>";
		} else if (today == "지난주") {
			document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$week_term.' days', SERVER_TIME)); ?>";
			document.getElementById("to_date").value = "<?php echo date('Y-m-d', strtotime('-'.($week_term - 6).' days', SERVER_TIME)); ?>";
		} else if (today == "지난달") {
			document.getElementById("fr_date").value = "<?php echo date('Y-m-01', strtotime('-1 Month', $last_term)); ?>";
			document.getElementById("to_date").value = "<?php echo date('Y-m-t', strtotime('-1 Month', $last_term)); ?>";
		} else if (today == "전체") {
			document.getElementById("fr_date").value = "";
			document.getElementById("to_date").value = "";
		}
	}
	
</script>

</div>    

<?
	dbClose($DB_con);
	$cntStmt = null;
	$stmt = null;
	$mcntStmt = null;
	$mstmt = null;

	 include "../common/inc/inc_footer.php";  //푸터 
	 
?>
