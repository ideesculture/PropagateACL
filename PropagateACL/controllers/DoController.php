<?php

class DoController extends ActionController {
	protected $config;		// plugin configuration file

	
	public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
		// Set view path for plugin views directory
		if (!is_array($pa_view_paths)) { $pa_view_paths = array(); }
		$pa_view_paths[] = __CA_APP_DIR__."/plugins/PropagateACL/themes/".__CA_THEME__."/views";
		
		// Load plugin configuration file
		$this->config = Configuration::load(__CA_APP_DIR__.'/plugins/PropagateACL/conf/propagateACL.conf');
		
		if (!$this->config->get('enabled')) {
			throw new ApplicationException(_t('PropagateACL is not enabled'));
		}

		parent::__construct($po_request, $po_response, $pa_view_paths);
		
		// Load plugin stylesheet
		MetaTagManager::addLink('stylesheet', __CA_URL_ROOT__."/app/plugins/PropagateACL/themes/".__CA_THEME__."/css/PropagateACL.css",'text/css');	
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	public function Object() {
		$user = (int) $this->request->getParameter('user', pInteger);
		$access = (int) $this->request->getParameter('access', pInteger);
		$item_id = (int) $this->request->getParameter('item_id', pInteger);
		$this->view->setVar("item_id", $item_id);

		$o_data = new Db();
		
		if($user) {
            // Récupère récursivement tous les descendants de l'objet
			$current_id = $item_id;
            $ids = [];
            $stack = [$item_id];
            while (!empty($stack)) {
                $current_id = array_pop($stack);
                $query = "SELECT object_id FROM ca_objects WHERE parent_id = ".$current_id." AND deleted = 0";
                $vt_results = $o_data->query($query);
                while($vt_results->nextRow()) {
                    $id = $vt_results->get("object_id");
                    $ids[] = $id;
                    $stack[] = $id;
                }
            }
            foreach ($ids as $id) {
                $query2 = "DELETE FROM ca_acl WHERE table_num=57 AND row_id = ".$id." AND user_id = ".$user;
                $o_data->query($query2);
                $query3 = "INSERT INTO ca_acl (table_num, row_id, user_id, access, notes, inherited_from_table_num, inherited_from_row_id) VALUES (57, ".$id.", ".$user.", ".$access.", 'propagated', 57, ".$item_id.")";
                $o_data->query($query3);
            }
			return $this->render("propagate_object_user_html.php");
		} else {
			$group = (int) $this->request->getParameter('group', pInteger);
            if($group) {
				$current_id = $item_id;
                // Récupère récursivement tous les descendants de l'objet
                $ids = [];
                $stack = [$item_id];
                while (!empty($stack)) {
                    $current_id = array_pop($stack);
                    $query = "SELECT object_id FROM ca_objects WHERE parent_id = ".$current_id." AND deleted = 0";
                    $vt_results = $o_data->query($query);
                    while($vt_results->nextRow()) {
                        $id = $vt_results->get("object_id");
                        $ids[] = $id;
                        $stack[] = $id;
                    }
                }
                foreach ($ids as $id) {
                    $query2 = "DELETE FROM ca_acl WHERE table_num=57 AND row_id = ".$id." AND group_id = ".$group;
                    $o_data->query($query2);
                    $query3 = "INSERT INTO ca_acl (table_num, row_id, group_id, access, notes, inherited_from_table_num, inherited_from_row_id) VALUES (57, ".$id.", ".$group.", ".$access.", 'propagated', 57, ".$item_id.")";
                    $o_data->query($query3);
                }

                return $this->render("propagate_object_group_html.php");
            }
		}
	// Default case: no user or group specified
	return $this->render("propagate_object_error_html.php");
	}

	public function RemoveExceptionObject() {
		$user = (int) $this->request->getParameter('user', pInteger);
		$item_id = (int) $this->request->getParameter('item_id', pInteger);
		$this->view->setVar("item_id", $item_id);

		$o_data = new Db();
		
		if($user) {
            // Récupère récursivement tous les descendants de l'objet
			$current_id = $item_id;
            $ids = [];
            $stack = [$item_id];
            while (!empty($stack)) {
                $current_id = array_pop($stack);
                $query = "SELECT object_id FROM ca_objects WHERE parent_id = ".$current_id." AND deleted = 0";
                $vt_results = $o_data->query($query);
                while($vt_results->nextRow()) {
                    $id = $vt_results->get("object_id");
                    $ids[] = $id;
                    $stack[] = $id;
                }
            }
            foreach ($ids as $id) {
                $query2 = "DELETE FROM ca_acl WHERE table_num=57 AND row_id = ".$id." AND user_id = ".$user;
				//print "<br>query2: ".$query2;
                $o_data->query($query2);
            }
			//die();
			$query3 = "DELETE FROM ca_acl WHERE table_num=57 AND row_id = ".$item_id." AND user_id = ".$user;
			$o_data->query($query3);
			return $this->render("propagate_removeaccessobject_user_html.php");
		} else {
			$group = (int) $this->request->getParameter('group', pInteger);
            if($group) {
				$current_id = $item_id;
                // Récupère récursivement tous les descendants de l'objet
                $ids = [];
                $stack = [$item_id];
                while (!empty($stack)) {
                    $current_id = array_pop($stack);
                    $query = "SELECT object_id FROM ca_objects WHERE parent_id = ".$current_id." AND deleted = 0";
                    $vt_results = $o_data->query($query);
                    while($vt_results->nextRow()) {
                        $id = $vt_results->get("object_id");
                        $ids[] = $id;
                        $stack[] = $id;
                    }
                }
                foreach ($ids as $id) {
                    $query2 = "DELETE FROM ca_acl WHERE table_num=57 AND row_id = ".$id." AND group_id = ".$group;
                    $o_data->query($query2);
					//print "<br>query2: ".$query2;
                }
				//die();
				$query3 = "DELETE FROM ca_acl WHERE table_num=57 AND row_id = ".$item_id." AND user_id = ".$user;
				$o_data->query($query3);

                return $this->render("propagate_removeaccessobject_group_html.php");
            }
		}
	// Default case: no user or group specified
	return $this->render("propagate_object_error_html.php");
	}
}