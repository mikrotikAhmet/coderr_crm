<aside id="menu" class="animated fadeIn">
	<ul class="nav metis-menu" id="side-menu">
		<li class="dashboard_user">
			<?php echo _l('welcome_top',$_staff->firstname); ?> <i class="fa fa-power-off top-left-logout pull-right" data-toggle="tooltip" data-title="<?php echo _l('nav_logout'); ?>" data-placement="left" onclick="window.location.href='<?php echo site_url('authentication/logout'); ?>'"></i>
		</li>
		<li class="quick-links">
			<div class="btn-group">
				<button type="button" class="btn btn-primary dropdown-toggle btn-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<?php
					foreach($_quick_actions as $key => $item){
						$url = '';
						if(isset($item['permission'])){
							if(!has_permission($item['permission'])){
								continue;
							}
						}
						if(isset($item['custom_url'])){
							$url = $item['url'];
						} else {
							$url = admin_url(''.$item['url']);
						}
						?>
						<li><a href="<?php echo $url; ?>"><?php echo $item['name']; ?></a></li>
						<?php } ?>
					</ul>
				</div>
			</li>
			<?php
			$menu_active = get_option('aside_menu_active');
			$menu_active = json_decode($menu_active);
			foreach($menu_active->aside_menu_active as $item){

				if(isset($item->permission) && !empty($item->permission)){
					if(!has_permission($item->permission)){
						continue;
					}
				}
				$submenu = false;
				$remove_main_menu = false;
				$url = '';
				if(isset($item->children)){
					$submenu = true;
					$total_sub_items_removed = 0;
					foreach($item->children as $_sub_menu_check){
						if(isset($_sub_menu_check->permission) && !empty($_sub_menu_check->permission)){
							if(!has_permission($_sub_menu_check->permission)){
								$total_sub_items_removed++;
							}
						}
					}
					if($total_sub_items_removed == count($item->children)){
						$submenu = false;
						$remove_main_menu = true;
					}
				} else {
					// child items removed
					if($item->url == '#'){continue;}
					$url = $item->url;
				}
				if($remove_main_menu == true){
					continue;
				}

				$name = _l($item->name);
				if(strpos($name,'translate_not_found') !== false){
					$name = $item->name;
				}
				$url = $item->url;
				if(!_startsWith($url,'http://')){
					$url = admin_url($url);
				}
				?>
				<li><a href="<?php echo $url; ?>"><i class="<?php echo $item->icon; ?> menu-icon"></i><?php echo $name; ?>
					<?php if($submenu == true){ ?>
					<span class="fa arrow"></span>
					<?php } ?>
				</a>
				<?php if(isset($item->children)){ ?>
				<ul class="nav nav-second-level collapse" aria-expanded="false">
					<?php foreach($item->children as $submenu){
						if(isset($submenu->permission) && !empty($submenu->permission)){
							if(!has_permission($submenu->permission)){
								continue;
							}
						}
						$name = _l($submenu->name);
						if(strpos($name,'translate_not_found') !== false){
							$name = $submenu->name;
						}

						$url = $submenu->url;
						if(!_startsWith($url,'http://')){
							$url = admin_url($url);
						}

						?>
						<li><a href="<?php echo $url; ?>">
							<?php if(!empty($submenu->icon)){ ?>
							<i class="<?php echo $submenu->icon; ?> menu-icon"></i>
							<?php } ?>
							<?php echo $name; ?></a></li>

							<?php } ?>
						</ul>
						<?php } ?>
					</li>
					<?php } ?>
					<li>
						<a href="#" class="open-customizer"><i class="fa fa-cog menu-icon"></i><?php echo _l('setting_bar_heading'); ?></a>
					</li>
				</ul>
			</aside>
