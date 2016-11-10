<div id="customize-sidebar" class="animated <?php if($this->session->has_userdata('customizer-open') && $this->session->userdata('customizer-open') == true){echo 'display-block';} ?>">
	<ul class="nav metis-menu">
		<li>
			<a href="#" class="btn btn-default close-customizer"><i class="fa fa-close"></i></a>
			<span class="text-left bold customizer-heading"><i class="fa fa-cog"></i> <?php echo _l('setting_bar_heading'); ?></span>
		</li>
		<?php
		$menu_active = get_option('setup_menu_active');
		$menu_active = json_decode($menu_active);
		foreach($menu_active->setup_menu_active as $item){
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

			$url = $item->url;
			if(!_startsWith($url,'http://')){
					$url = admin_url($url);
			}

			$name = _l($item->name);
			if(strpos($name,'translate_not_found') !== false){
				$name = $item->name;
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
					<a href="http://www.macropay.net/documentations" target="_blank">Help</a>
				</li>
			</ul>
		</div>
