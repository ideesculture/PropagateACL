<?php
 
	class PropagateACLPlugin extends BaseApplicationPlugin {
		# -------------------------------------------------------
		/**
		 *
		 */
		protected $description = null;
		
		/**
		 *
		 */
		private $opo_config;
		
		/**
		 *
		 */
		private $ops_plugin_path;
		# -------------------------------------------------------
		public function __construct($ps_plugin_path) {
			$this->ops_plugin_path = $ps_plugin_path;
			$this->description = _t('propagateACL Plugin');
			
			parent::__construct();
			
			$this->opo_config = Configuration::load($ps_plugin_path.'/conf/propagateACL.conf');
		}

		# -------------------------------------------------------
		/**
		 * Insert into ObjectEditor info (side bar)
		 */
		public function hookAppendToEditorInspector(array $va_params = array()) {
			//MetaTagManager::addLink('stylesheet', __CA_URL_ROOT__."/app/plugins//museesDeFrance/assets/css/museesDeFrance.css",'text/css');

			$t_item = $va_params["t_item"];

			// basic zero-level error detection
			if (!isset($t_item)) return false;

			// fetching content of already filled vs_buf_append to surcharge if present (cumulative plugins)
			if (isset($va_params["vs_buf_append"])) {
				$vs_buf = $va_params["vs_buf_append"];
			} else {
				$vs_buf = "";
			}

			if($va_params["caEditorInspectorAppend"]) {
				$vs_buf = $va_params["caEditorInspectorAppend"];
			}
			
			$vs_table_name = $t_item->tableName();
			$vn_item_id = $t_item->getPrimaryKey();
			$vn_code = $t_item->getTypeCode();

			if ($vs_table_name == "ca_objects") {
				// Add the Propagate ACL link to the inspector
				/*$vs_buf .= '<div class="inspector-item">';
				$vs_buf .= '<h3>' . _t('Propagate ACL') . '</h3>';
				$vs_buf .= '<p>' . _t('This object is part of a Propagate ACL configuration.') . '</p>';
				$vs_buf .= '<a href="' . __CA_URL_ROOT__ . '/propagateACL/edit/' . $vn_item_id . '" class="btn btn-primary">' . _t('Edit Propagate ACL') . '</a>';
				$vs_buf .= '</div>';*/
				$vs_buf .= '<script>
				$(".button.labelInfo.caAddItemButton").click(setTimeout(function() {
				// search for the input fields editUserAccess
					$("#editUserAccess input.lookupBg").each(function(index) {
						let user_group = $(this).parent().find("input[type=hidden]").val();
						let access = $(this).parent().find("select").val();
						console.log("access", access);

						//check if $(this) id contains the letters "_group_"
						if ($(this).attr("id").indexOf("_group_") > -1) {
							console.log("group",user_group);
							let button = $(
								\'<form method="post" action="/gestion/index.php/PropagateACL/Do/Object/id/'.$vn_item_id.'"><input type="hidden" name="group" value="\'+user_group+\'"><input type="hidden" name="access" value="\'+access+\'"><input type="hidden" name="item_id" value="'.$vn_item_id.'"><button>Propager les droits</button></form>\'
								+\'<form method="post" action="/gestion/index.php/PropagateACL/Do/RemoveExceptionObject/id/'.$vn_item_id.'"><input type="hidden" name="group" value="\'+user_group+\'"><input type="hidden" name="item_id" value="'.$vn_item_id.'"><button>Supprimer cette exception</button></form>\'
							);
							$(this).parent().append(button);
						} else {
							console.log("user",user_group);
							let button = $(
								\'<form method="post" action="/gestion/index.php/PropagateACL/Do/Object/id/'.$vn_item_id.'"><input type="hidden" name="user" value="\'+user_group+\'"><input type="hidden" name="access" value="\'+access+\'"><input type="hidden" name="item_id" value="'.$vn_item_id.'"><button>Propager les droits</button></form>\'
								+\'<form method="post" action="/gestion/index.php/PropagateACL/Do/RemoveExceptionObject/id/'.$vn_item_id.'"><input type="hidden" name="user" value="\'+user_group+\'"><input type="hidden" name="item_id" value="'.$vn_item_id.'"><button>Supprimer cette exception</button></form>\'
							);
							$(this).parent().append(button);
						}		
					});
					
				}, 1000));</script>';
				
			}
			$va_params["caEditorInspectorAppend"] = $vs_buf;
			return $va_params;
		}
		# -------------------------------------------------------
		/**
		 * Override checkStatus() to return true - the statisticsViewerPlugin always initializes ok... (part to complete)
		 */
		public function checkStatus() {
			return array(
				'description' => $this->getDescription(),
				'errors' => array(),
				'warnings' => array(),
				'available' => ((bool)$this->opo_config->get('enabled'))
			);
		}
		# -------------------------------------------------------
	}
