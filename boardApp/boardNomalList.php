				<?
				$bFileUpload = $b_UploadCnt;

				if ($totalCnt < 1) { //없을 경우
					$chkMResult = "0";
					$listInfoResult = array("totCnt" => (int)$totalCnt, "page" => (int)$page);
				} else {
					$chkMResult = "1";

					$mdata  = [];
					while ($n = $nqStmt->fetch(PDO::FETCH_ASSOC)) {
						$bNot = trim($n['is_notice']);  //공지사항여부
						if ($bNot == 'Y') {
							$bNot = 1;
						} else {
							$bNot = 0;
						}
						// $nbChk = trim($n['b_Chk']); //상담문의 여부
						// if ($nbChk == 'Y') {
						// 	$nbChk = true;
						// } else {
						// 	$nbChk = false;
						// }
						// $nbNIdx = trim($n['b_NIdx']);
						$nTitle = trim($n['title']);
						$nsubject = stripslashes($nTitle);
						// $nContent = trim($n['content']);
						// $nContent = str_replace("\r","",$nContent);
						// $nContent = str_replace("\n","",$nContent);
						$nRegDate = DateHard($n['reg_date'], 2);
						$link = $web_url . "/boardApp/notice.php?idx=" . $n["idx"];

						if ($bFileUpload > 0) {
							# 파일첨부  조회
							$nbFileQuery = " SELECT idx, name, original_name FROM TB_BOARD_FILE WHERE board_idx = :board_idx";
							$nbFileQuery .= " ORDER BY idx DESC";

							//echo $nbFileQuery."<BR>";
							//exit;
							$nbFileStmt = $DB_con->prepare($nbFileQuery);
							$nbFileStmt->bindparam(":board_idx", $n['idx']);
							$nbFileStmt->execute();
							$nbFileNum = $nbFileStmt->rowCount();
						}


						if ($nbFileNum < 1) { //아닐경우
							$imgNUrl = "";
							$width = 0;
							$height = 0;
						} else {

							$mimg  = [];
							while ($i = $nbFileStmt->fetch(PDO::FETCH_ASSOC)) {
								$nCIdx = trim($i['idx']);
								$nb_name = trim($i['name']);

								$no_name = explode(".", $i['original_name']);
								$nfileExt = strtolower($nb_name[strlen($nb_name) - 1]);   //확장자 구하는것

								$nimgUrl = "/data/" . $b_upload . "/";

								if ($nfileExt == "gif" || $nfileExt == "jpeg" || $nfileExt == "jpg" || $nfileExt == "png" || $nfileExt == "bmp") {  //확장자 이미지 체크
									$imgNUrl = $nimgUrl . $nbFName;

									$chkImg = DATA_PATH . "/" . $b_upload . "/" . $nbFName;

									if (is_file($chkImg)) {
										$img_info = @getimagesize($chkImg);
										//print_r($img_info)."<BR>";
										$width = $img_info['0']; //입력받은 파일의 가로크기
										$width = (int)$width;
										$height = $img_info['1']; //입력받은 파일의 세로크기
										$height = (int)$height;
									}
								}


								if ($imgNUrl <> "") {  //이미지가 있을 경우
									$mimgUrl = array("idx" => (int)$nCIdx, "imgUrl" => (string)$imgNUrl, "width" => (int)$width, "height" => (int)$height);
								} else {
								}


								array_push($mimg, $mimgUrl);
							}
						}


						$mresult = ["board_idx" => (int)$n["idx"], "type" => (int)$type, "notice" => $bNot, "title" => (string)$nsubject, "link" => $link, "regDate" => (string)$nRegDate];

						if ($imgNUrl <> "") {  //이미지가 있을 경우
							$mresult["list"] = $mimg;  //이미지목록
						}

						array_push($mdata, $mresult);
					}
				}


				if ($counts < 1) { //없을 경우
					$chkResult = "0";
					$listInfoResult = (int)$totalCnt;
				} else {

					$chkResult = "1";
					$listInfoResult = (int)$totalCnt;

					$data  = [];
					while ($v = $qStmt->fetch(PDO::FETCH_ASSOC)) {
						$bNIdx = trim($v['b_NIdx']);
						$bChk = trim($v['b_Chk']); //상담문의 여부
						if ($bChk == 'Y') {
							$bChk = true;
						} else {
							$bChk = false;
						}
						$tTitle = trim($v['b_Title']);
						$subject = cut_str(stripslashes($tTitle), $b_TitCnt);
						$content = trim($v['b_Content']);
						// $content = str_replace("\r","",$content);
						// $content = str_replace("\n","",$content);


						$regDate = DateHard($v['reg_Date'], 2);


						if ($bFileUpload > 0) {
							# 파일첨부  조회
							$bFileQuery = "";
							$bFileQuery = " SELECT idx, b_Idx, b_NIdx, b_FIdx, b_FName, b_OFName, b_FSize FROM TB_BOARD_FILE WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx ";
							$bFileQuery .= " ORDER BY b_FIdx DESC";
							$bFileStmt = $DB_con->prepare($bFileQuery);
							$bFileStmt->bindparam(":b_Idx", $board_id);
							$bFileStmt->bindparam(":b_NIdx", $bNIdx);
							$bFileStmt->execute();
							$bFileNum = $bFileStmt->rowCount();
						}


						if ($bFileNum < 1) { //아닐경우
							$imgUrl = "";
							$width = 0;
							$height = 0;
						} else {

							$cimg  = [];
							while ($k = $bFileStmt->fetch(PDO::FETCH_ASSOC)) {
								$cidx = trim($k['idx']);
								$bFName = trim($k['b_FName']);

								$fname = explode(".", $k['b_FName']);
								$fileExt = strtolower($fname[count($fname) - 1]);   //확장자 구하는것

								$cimgUrl = "/data/" . $b_Upload . "/";

								if ($fileExt == "gif" || $fileExt == "jpeg" || $fileExt == "jpg" || $fileExt == "png" || $fileExt == "bmp") {  //확장자 이미지 체크
									$imgUrl = $cimgUrl . $bFName;

									$chkTImg = DATA_PATH . "/" . $b_Upload . "/" . $bFName;

									if (is_file($chkTImg)) {
										$imgTinfo = @getimagesize($chkTImg);
										//print_r($imgTinfo)."<BR>";
										$width = $imgTinfo['0']; //입력받은 파일의 가로크기
										$width = (int)$width;
										$height = $imgTinfo['1']; //입력받은 파일의 세로크기
										$height = (int)$height;
									}
								}


								if ($imgUrl <> "") {  //이미지가 있을 경우
									$mimgUrl = array("idx" => (int)$cidx, "imgUrl" => (string)$imgUrl, "width" => (int)$width, "height" => (int)$height);
								} else {
								}

								array_push($cimg, $mimgUrl);
							}
						}


						$result = ["boardId" => (int)$board_id, "idx" => (int)$bNIdx, "bChk" => $bChk, "title" => (string)$subject, "regDate" => (string)$regDate, "conTent" => (string)$content];

						if ($imgUrl <> "") {  //이미지가 있을 경우
							$result["imgLists"] = $cimg;  //이미지목록
						}

						array_push($data, $result);
					}

					$chkData = [];
					$chkData["result"] = true;
					$chkData["totalCnt"] = $listInfoResult;  //카운트 관련
					$chkData["page"] = $page;  //페이지 관련
					$chkData["lastPage"] = (int)ceil($totalCnt / 10);  //페이지 관련
					
					if ($b_CateChk == "Y") {  //카테고리 사용여부
						$chkData['cateLists'] = $bcate;
					}
					
					if ($bNot <> "") {
						$chkData["nlists"] = $mdata;  //공지사항목록
					}
					$chkData['lists'] = $data;
				}
				
				if ($chkMResult  == "1" && $chkResult  == "1") {
					$output = str_replace('\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT));
				} else if ($chkMResult  == "1" && $chkResult  == "0") {
					$chkData2["result"] = true;
					$chkData2["totalCnt"] = $listInfoResult;  //카운트 관련
					$chkData2["page"] = $page;  //페이지 관련
					$chkData2["lastPage"] = (int)ceil($totalCnt / 10);  //페이지 관련
					
					if ($b_CateChk == "Y") {  //카테고리 사용여부
						$chkData2['cateLists'] = $bcate;
					}
					
					if ($bNot <> "") {
						$chkData2["list"] = $mdata;  //공지사항목록
					}
					
					$output = str_replace('\/', '/', json_encode($chkData2, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT));
				} else {
					$chkData2["result"] = true;
					$chkData2["totalCnt"] = $listInfoResult;  //카운트 관련
					$chkData2["page"] = $page;  //페이지 관련
					$chkData2["lastPage"] = (int)ceil($totalCnt / 10);  //페이지 관련

					if ($b_CateChk == "Y") {  //카테고리 사용여부
						$chkData2['cateLists'] = $bcate;
					}

					$output = str_replace('\/', '/', json_encode($chkData2, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT));
				}

				echo urldecode($output);




				?>

