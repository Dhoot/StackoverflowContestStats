<!DOCTYPE html>
<html lang="en">
<head>
	<title>Stackoverflow contest - Stats</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="https://cdn.sstatic.net/Sites/stackoverflow/img/favicon.ico?v=4f32ecc8f43d"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body>
	<div class="limiter">
		<div class="container-table100">
			<div class="wrap-table100">
				<div class="text-center">
					<img src="https://www.corevaluetech.com/themes/custom/progressive_sub/logo.png" /> 
				</div>		
				<h2 class="text-center" style="color: #666;">Stackoverflow contest - Stats</h2>
				<hr />
				<div class="table100 ver2 m-b-110">
				<?php if(!(isset($_POST["submit"]) && isset($_FILES["file"]))) { ?>
					<form action="" method="post" enctype="multipart/form-data">
					<div class="table100-body js-pscroll">
						<table>
							<tbody>
								<tr class="row100 body">
									<td class="cell100 column1">Select CSV to upload:</td>
									<td class="cell100 column2"><input type="file" name="file" required id="file" accept=".csv"></td>
								</tr>
								<tr class="row100 body">
									<td class="cell100 column1">&nbsp;</td>
									<td class="cell100 column2">&nbsp;</td>
								</tr>
								<tr class="row100 body">
									<td class="cell100 column1"><input type="reset" value="Reset" class="btn btn-secondary" name="reset"></td>
									<td class="cell100 column2"><input type="submit" value="Upload CSV"  class="btn btn-info" name="submit"></td>
								</tr>
							</tbody>
						</table>
					</div>
					</form>
				 <?php 
				  } else { 
				  	$csvAsArray = array_map('str_getcsv', file($_FILES["file"]["tmp_name"]));
				    $users = [];
				    $userParam = '';
				    foreach ($csvAsArray as $rowNo => $row) {
				      if($rowNo === 0) continue;
				      $userId = explode('/',explode('users/',$row[1])[1])[0];
				      $users[$userId] = (object) ['givenName' => $row[0], 'startRepo' => $row[2]];
				      if($rowNo+1  === count($csvAsArray))
				        $userParam .= $userId;
				      else   
				        $userParam .= $userId.'%3B';
				    }
				    $stackoverflowAPIKey = 'REPLACE_THIS';
				    $apiUrl = 'https://api.stackexchange.com/2.2/users/'.$userParam.'?key='.$stackoverflowAPIKey.'&site=stackoverflow&order=desc&sort=reputation&filter=default';

				    $opts = array(
						'http'=>array(
							'method'=>"GET",
							'header'=>"Accept-Language: en-US,en;q=0.8rn" .
										"Accept-Encoding: gzip,deflate,sdchrn" .
										"Accept-Charset:UTF-8,*;q=0.5rn" .
										"User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:19.0) Gecko/20100101 Firefox/19.0 FirePHP/0.4rn	"
						)
					);

					$context = stream_context_create($opts);
				    $content = file_get_contents($apiUrl,false,$context);
					$stackOverFlowData = json_decode(gzdecode($content));

				    foreach ($stackOverFlowData->items  as $user) {
				        $users[$user->user_id]->currentRepo = $user->reputation;
				        $users[$user->user_id]->pic = $user->profile_image;
				        $users[$user->user_id]->displayName = $user->display_name; 
				        $users[$user->user_id]->badges = $user->badge_counts;
				        $users[$user->user_id]->link = $user->link;
				    }

				    usort($users, function ($a, $b) {return ($b->currentRepo-$b->startRepo) - ($a->currentRepo-$a->startRepo);});
				  	?>
				  	<div class="table100-head">
						<table>
							<thead>
								<tr class="row100 head">
									<th class="cell100 column1">User Profile</th>
									<th class="cell100 column2">Start Repo</th>
									<th class="cell100 column3">Current Repo</th>
									<th class="cell100 column4">Progress</th>
									<th class="cell100 column5">Badges</th>
								</tr>
							</thead>
						</table>
					</div>
					<div class="table100-body js-pscroll">
						<table>
							<tbody>
								<?php 
								foreach ($users as $userId => $user) {
							      print '<tr class="row100 body">';
							      print '<td class="cell100 column1"><img src="'.$user->pic.'"><br/>'.$user->givenName.' (<a href="'.$user->link.'" target="_blank">'.$user->displayName.'</a>)</td>';
							      print '<td class="cell100 column2">'.$user->startRepo.'</td>';
							      print '<td class="cell100 column3">'.$user->currentRepo.'</td>';
							      print '<td class="cell100 column4">'.($user->currentRepo - $user->startRepo).'</td>';
							      print '<td class="cell100 column5">';
							      foreach ($user->badges as $badge => $badgeCount) {
							        print $badgeCount.' x '.$badge.'<br/>';
							      }
							      print '</td>';
							      print '</tr>';
							    }
							    ?>
							</tbody>
						</table>
					</div>
					<?php 
					}
					?>	
				</div>
			</div>
		</div>
	</div>


<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	<script>
		$('.js-pscroll').each(function(){
			var ps = new PerfectScrollbar(this);

			$(window).on('resize', function(){
				ps.update();
			})
		});
			
		
	</script>

</body>
</html>