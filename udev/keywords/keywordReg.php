<?
$menu = "4";
$smenu = "1";

include "../common/inc/inc_header.php";  //헤더

$DB_con = db1();

if ($mode == "mod") {
    $titNm = "키워드 수정";

    $query = "SELECT idx, name, reg_date, search_date FROM keyword WHERE idx = :idx";

    $stmt = $DB_con->prepare($query);
    $stmt->bindparam(":idx", $idx);
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num < 1) { //아닐경우
    } else {

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $kwd_idx = trim($row['idx']);
            $kwd_nm =  trim($row['name']);
            $reg_date = $row['reg_date'];
            $search_date = $row['search_date'];
        }
    }
} else {
    $titNm = "키워드 등록";
}

$qstr = "fr_date=" . urlencode($fr_date) . "&amp;to_date=" . urlencode($to_date) . "&amp;findType=" . urlencode($findType) . "&amp;findword=" . urlencode($findword);

include "../common/inc/inc_gnb.php";  //헤더 
include "../common/inc/inc_menu.php";  //메뉴 

?>

<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?= $titNm ?></h1>
        <div class="container_wr">
            <form name="fmember" id="fmember" action="keywordProc.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" name="mode" id="mode" value="<?= $mode ?>">
                <input type="hidden" name="idx" id="idx" value="<?= $idx ?>">
                <input type="hidden" name="qstr" id="qstr" value="<?= $qstr ?>">
                <input type="hidden" name="page" id="page" value="<?= $page ?>">

                <div class="tbl_frm01 tbl_wrap">
                    <table>
                        <caption><?= $titNm ?></caption>
                        <colgroup>
                            <col class="grid_4">
                            <col>
                            <col class="grid_4">
                            <col>
                        </colgroup>
                        <tbody>
                            <tr>
                                <? if ($mode == "mod") { ?>
                                    <th scope="row"><label for="kwd_nm">키워드명</label></th>
                                    <td colspan="3">
                                        <input type="text" name="kwd_nm" id="kwd_nm" class="frm_input required" size="50" maxlength="20" value="<?= $kwd_nm ?>">
                                    </td>
                                <? } else if ($mode == "reg") { ?>
                                    <th scope="row"><label for="kwd_nm">키워드명<strong class="sound_only">필수</strong></label></th>
                                    <td>
                                        <input type="text" name="kwd_nm" value="" id="kwd_nm" required class="frm_input required" size="50" maxlength="20">
                                    </td>
                                <? } ?>
                            </tr>
                            <? if ($mode == "mod") { ?>
                                <tr>
                                    <th scope="row"><label for="reg_date">최초 생성일</label></th>
                                    <td>
                                        <?= $reg_date ?>
                                        <input type="hidden" name="reg_date" id="reg_date" value="<?= $reg_date ?>">
                                    </td>
                                    <th scope="row"><label for="search_date">최근 뉴스 검색일</label></th>
                                    <td>
                                        <?= $search_date ?>
                                        <input type="hidden" name="search_date" id="search_date" value="<?= $search_date ?>">
                                    </td>
                                </tr>
                            <? } ?>
                        </tbody>
                    </table>
                </div>

                <div class="btn_fixed_top">
                    <a href="keywordsList.php?<?= $qstr ?>&page=<?= $page ?>" class="btn btn_02">목록</a>
                    <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
                </div>
            </form>


            <script>
                function fmember_submit(f) {
                    return true;
                }
            </script>

        </div>

        <?
        dbClose($DB_con);
        $stmt = null;
        $meInfoStmt = null;
        $mEtcStmt = null;
        $mstmt = null;

        include "../common/inc/inc_footer.php";  //푸터 

        ?>