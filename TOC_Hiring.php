<?php
					$types = Array(
							Array('#080808','Technology'),
							Array('#b2df8a', 'Design-Media'), 
							Array('#33a02c', 'Life Sciences'),
							Array('#e31a1c', 'Consumer Products'),
							Array('#6a3d9a', 'Misc')
							);
					$marker_id = 0;
					
					$_linefix = function ($str){
						$str = str_replace( "\r", "<br>", $str );
						$str = str_replace( "\n", "<br>", $str );
						$firstwords = explode( $str, " " );
						if( sizeof( $firstwords ) > 4 ){
							$firstwords = $firstwords[0] . " " . $firstwords[1];
							if(strpos( $str, $firstwords ) < strrpos( $str, $firstwords ) ){
								// assume a repeat
								$str = explode( $str, $firstwords );
								$str = $firstwords . $str[0];
							}
						}
						return $str;
				 };
					
					foreach($types as $type) {
						$places = pg_query("SELECT * FROM places WHERE approved='1' AND hiring= '1' AND type='$type[1]' ORDER BY title");
						$places_total = pg_num_rows($places);
						while($place = pg_fetch_assoc($places)) {
							$place[title] = htmlspecialchars_decode(addslashes(htmlspecialchars($_linefix($place[title]))));
							if($place[lat] == 0 && $place[lng] == 0){
								// bad location
								echo "/* bad location: " . $place[title] . " */";
								continue;
							}
							$place[description] = htmlspecialchars_decode(addslashes(htmlspecialchars($_linefix($place[description]))));
							$place[uri] = addslashes(htmlspecialchars($_linefix($place[uri])));
							$place[address] = htmlspecialchars_decode(addslashes(htmlspecialchars($_linefix($place[address]))));
							echo "
								markers.push(['".$place[title]."', '".$place[type]."', '".$place[lat]."', '".$place[lng]."', '".$place[description]."', '".$place[uri]."', '".$place[address]."', ".$place[id].", '".$place[hiring]."', '".$place[hirelink]."']); 
								markerTitles[".$marker_id."] = '".$place[title]."';
							"; 
							$count[$place[type]]++;
							$marker_id++;
						}
					}
				?>