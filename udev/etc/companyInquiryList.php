<?
    //  ini_set("display_errors", 1);
	$menu = "3";
	$smenu = "6";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;

	$sql_search=" WHERE 1";

	if($findword != "")  {
		$sql_search .= " AND `{$findType}` LIKE '%{$findword}%' ";
	}

	$DB_con = db1();
	
	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(idx) AS cntRow ";
	$cntQuery .= ",SUM(CASE WHEN contact_Comment IS NULL THEN 1 ELSE 0 END) AS I_CNT ";
	$cntQuery .= ",SUM(CASE WHEN contact_Comment IS NOT NULL THEN 1 ELSE 0 END) AS F_CNT ";
	$cntQuery .= "FROM TB_COMPANY_CONTACT  {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);

	if ($fr_date != "" || $to_date != "" ) {
	    $cntStmt->bindValue(":fr_date",$fr_date);
	    $cntStmt->bindValue(":to_date",$to_date);
	}

	if($findword != "")  {
	    $cntStmt->bindValue(":findType",$findType);		
	    $cntStmt->bindValue(":findword",$findword );
	}

	$findType = trim($findType);
	$findword = trim($findword);

	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalCnt = $row['cntRow'];
	$I_CNT = $row['I_CNT'];
	$F_CNT = $row['F_CNT'];

	$cntStmt = null;

	$rows = 10;
	$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
	if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
	$from_record = ($page - 1) * $rows; // 시작 열을 구함


	if (!$sort1)	{
		$sort1  = "reg_Date";
		$sort2 = "DESC";
	}

	$sql_order = "order by $sort1 $sort2";

	//목록
	$query = "";
	$query = "SELECT idx, contact_Name, contact_Email, contact_Tel, contact_Content, contact_Comment, reg_Date FROM TB_COMPANY_CONTACT {$sql_search} {$sql_order} limit  {$from_record}, {$rows}" ;
	$stmt = $DB_con->prepare($query);

	if($findword != "")  {
	    $stmt->bindValue(":findType",$findType);		
	    $stmt->bindValue(":findword",$findword );
	}

	$findType = trim($findType);
	$findword = trim($findword);

	$stmt->execute();
	$numCnt = $stmt->rowCount();

	$qstr = "findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script type="text/javascript" src="<?=UDEV_DIR?>/etc/js/event.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">문의 관리</h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 등록 </span><span class="ov_num"><?=number_format($totalCnt);?>건 </span>&nbsp;
			<span class="btn_ov01"><span class="ov_txt">총 답변대기 </span><span class="ov_num"><?=number_format($I_CNT);?>건 </span>&nbsp;
			<span class="btn_ov01"><span class="ov_txt">총 답변완료 </span><span class="ov_num"><?=number_format($F_CNT);?>건 </span>
		</div>
		<form class="local_sch03 local_sch"  autocomplete="off">
		<div>
			<strong>분류</strong>
			<select name="findType" id="findType">
				<option value="contact_Content" <?if($findType=="contact_Content"){?>selected<?}?>>제목</option>
			</select>
			<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
			<input type="text" name="findword" id="findword" value="<?=$findword?>" size="30"  class=" frm_input">

			<input type="submit" value="검색" class="btn_submit">
			<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
		</div>
		</form>



<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>이벤트 배너 목록</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
        <th scope="col">순번</th>
        <th scope="col">문의자 이름</th>
        <th scope="col">문의자 이메일</th>
		<th scope="col">문의자 전화번호</a></th>		 
		<th scope="col">문의내용</th>
		<th scope="col">문의일자</th>
		<th scope="col">관리자 메모 여부</th>
		<th scope="col">관리</th>
    </tr>
    </thead>
    <tbody>

    <?

	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row =$stmt->fetch()) {
       // $bg = 'bg'.($stmt->fetch()%2);
		    $from_record++;	
		$contact_Name = $row['contact_Name'];
        $contact_Email = $row['contact_Email'];
		if($contact_Email == ''){
			$contactEmail = '-';
		}else{
			$contactEmail = $contact_Email;
		}
        $contact_Tel = $row['contact_Tel'];
		if($contact_Tel == ''){
			$contactTel = '-';
		}else{
			$contactTel = $contact_Tel;
		}
		$contact_Content = $row['contact_Content'];
		if (mb_strlen($contact_Content, "UTF-8") > 40) {
			$contact_Content = mb_substr($contact_Content, 0, 40, "UTF-8") . " ...";
		}
        $contact_Comment = $row['contact_Comment'];
		if($contact_Comment == ''){
			$contactComment = '-';
		}else{
			$contactComment = '등록된 메모가 있습니다.';
		}
        $reg_Date = $row['reg_Date'];
    ?>
    <tr class="<?=$bg?>">
		<td><?=$from_record?></td>
		<td><?=$contact_Name?></td>
		<td><?=$contactEmail?></td>
		<td><?=$contactTel?></td>
		<td><div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap; ">
			<?if($contact_Comment == ''){?>
				<a href="companyInquiryReg.php?mode=reg&idx=<?=$row['idx']?>&<?=$qstr?>&page=<?=$page?>"><span><?=	$contact_Content ?></span></a>
			<?}else{?>
				<a href="companyInquiryReg.php?mode=mod&idx=<?=$row['idx']?>&<?=$qstr?>&page=<?=$page?>"><span><?= $contact_Content ?></span></a>
			<?}?></div></td>
		<td><?=$reg_Date?></td>
		<td><?=$contactComment?></td>
		<td headers="mb_list_mng" class="td_mng td_mng_s">
        <?if($contact_Comment == ''){?>
            <a href="companyInquiryReg.php?mode=reg&idx=<?=$row['idx']?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_03">메모등록</a>
        <?}else{?>
            <a href="companyInquiryReg.php?mode=mod&idx=<?=$row['idx']?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_02">메모수정</a>
        <?}?>
		</td>
    </tr>
    <? 

		}
	?>   
	<? } else { ?>
	<tr>
		<td colspan="8" class="empty_table">자료가 없습니다.</td>
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

	function fvisit_submit(act)
	{
		var f = document.fvisit;
		f.action = act;
		f.submit();
	}

</script>

</div>    

<?
	dbClose($DB_con);
	$cntStmt = null;
	$stmt = null;

	 include "../common/inc/inc_footer.php";  //푸터 
	 
?>
