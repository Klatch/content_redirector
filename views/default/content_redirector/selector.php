<?php
$type_selection = "";
$container_selection = "";
$group_selection = "";

$supported_plugins = array(
	"blog" => array(
		"title" => elgg_echo("item:object:blog"),
		"group_tool_option" => "blog_enable",
		"user_link" => "blog/add/[GUID]",
		"group_link" => "blog/add/[GUID]"
	),
	"bookmarks" => array(
		"title" => elgg_echo("item:object:bookmarks"),
		"group_tool_option" => "bookmarks_enable",
		"user_link" => "bookmarks/add/[GUID]",
		"group_link" => "bookmarks/add/[GUID]",
	),
	"event_manager" => array(
		"title" => elgg_echo("event_manager:event:view:event"),
		"group_tool_option" => "event_manager_enable",
		"user_link" => "events/event/new",
		"group_link" => "events/event/new/[GUID]",
	),
	"etherpad" => array(
		"title" => elgg_echo("item:object:etherpad"),
		"group_tool_option" => "etherpad_enable",
		"user_link" => "etherpad/add/[GUID]",
		"group_link" => "etherpad/add/[GUID]"
	),
	"file" => array(
		"title" => elgg_echo("item:object:file"),
		"group_tool_option" => "file_enable",
		"user_link" => "file/add/[GUID]",
		"group_link" => "file/add/[GUID]"
	),
	"media" => array(
		"title" => elgg_echo("item:object:media"),
		"group_tool_option" => "media_enable",
		"user_link" => "media/add",
		"group_link" => "media/group/[GUID]"
	),
	"mood" => array(
		"title" => elgg_echo("item:object:mood"),
		"group_tool_option" => "mood_enable",
		"user_link" => "mood/owner/[USERNAME]",
		"group_link" => "mood/group/[GUID]"
	),
	"pages" => array(
		"title" => elgg_echo("item:object:page"),
		"group_tool_option" => "pages_enable",
		"user_link" => "pages/add/[GUID]",
		"group_link" => "pages/add/[GUID]",
	),
	"polls" => array(
		"title" => elgg_echo("item:object:poll"),
		"group_tool_option" => "polls_enable",
		"user_link" => "polls/add",
		"group_link" => "polls/add/[GUID]"
	),
	"snippets" => array(
		"title" => elgg_echo("item:object:snippets"),
		"group_tool_option" => "snippets_enable",
		"user_link" => "snippets/add/[GUID]",
		"group_link" => "snippets/add/[GUID]"
	),
	"thewire" => array(
		"title" => elgg_echo("item:object:thewire"),
		"group_tool_option" => "thewire_enable",
		"user_link" => "thewire/owner/[USERNAME]",
		"group_link" => "thewire/group/[GUID]"
	),
	"tidypics" => array(
		"title" => elgg_echo("item:object:album"),
		"group_tool_option" => "photos_enable",
		"user_link" => "photos/new/[USERNAME]",
		"group_link" => "photos/new/[USERNAME]"
	),
	"videolist" => array(
		"title" => elgg_echo("item:object:videolist"),
		"group_tool_option" => "videolist_enable",
		"user_link" => "videolist/add/[GUID]",
		"group_link" => "videolist/add/[GUID]"
	)
);

foreach($supported_plugins as $plugin_id => $plugin_details){
	if(elgg_is_active_plugin($plugin_id)){
		$type_selection .= elgg_view("input/button", array("id" => $plugin_id, "value" => $plugin_details["title"])) . " ";
	}
}

if(elgg_is_active_plugin("groups")){
	// check for membership
	$options = array(
			"type" => "group",
			"limit" => false,
			"relationship" => "member",
			"relationship_guid" => elgg_get_logged_in_user_guid()
		);

	$groups = elgg_get_entities_from_relationship($options);
	if(!empty($groups)){
		// personal or group
		$container_selection .= elgg_view("input/button", array("id" => "content-redirector-selector-container-personal", "value" => elgg_echo("content_redirector:selector:container:personal"))) . " ";
		$container_selection .= elgg_view("input/button", array("id" => "content-redirector-selector-container-group", "value" => elgg_echo("content_redirector:selector:container:group")));
		$group_selection_items = array();

		$group_tools_options = elgg_get_config("group_tool_options");

		foreach($groups as $group){
			$group_rels = array();
			foreach($supported_plugins as $plugin_name => $plugin_details){
				if($plugin_details["group_tool_option"] === true){
					$group_rels[] = $plugin_name;
				} else {
					$tool_option = $group->$plugin_details["group_tool_option"];
					if($tool_option == "yes"){
						$group_rels[] = $plugin_name;
					} elseif($tool_option !== "no") {
						if($group_tools_options){
							foreach ($group_tools_options as $i => $option) {
								if ($option->name == str_replace("_enable", "", $plugin_details["group_tool_option"])) {
									if($option->default_on == true){
										$group_rels[] = $plugin_name;
									}
									break;
								}
							}
						}
					}
				}
			}
			if(!empty($group_rels)){
				$group_rels = implode($group_rels, " ");
			}

			$key = strtolower($group->name) . "-" . $group->getGUID();
			$button = elgg_view("input/button", array("id" => $group->guid, "rel" => $group_rels, "name" => $group->username, "value" => $group->name));
			$group_selection_items[$key] = $button;
		}
		ksort($group_selection_items);
		$group_selection = implode($group_selection_items, " ");
	}
}

if(!empty($type_selection)){
	?>
<div id='content-redirector-selector'>
	<?php
	$type_selection = "<div class='elgg-subtext'>" . elgg_echo("content_redirector:selector:type:info")  . "</div>" . $type_selection;
	echo elgg_view_module("info", elgg_echo("content_redirector:selector:type"), $type_selection, array("id" => "content-redirector-type-selection"));

	if(!empty($container_selection)){
		$container_selection = "<div class='elgg-subtext'>" . elgg_echo("content_redirector:selector:container:info")  . "</div>" . $container_selection;
		echo elgg_view_module("info", elgg_echo("content_redirector:selector:container"), $container_selection, array("id" => "content-redirector-container-selection", "class" => "hidden"));
	}

	if(!empty($group_selection)){
		$group_selection = "<div class='elgg-subtext'>" . elgg_echo("content_redirector:selector:group:info")  . "</div>" . $group_selection;
		$group_selection .= "<div id='content-redirector-group-none'>". elgg_echo("content_redirector:selector:group:none") . "</div>";
		echo elgg_view_module("info", elgg_echo("content_redirector:selector:group"), $group_selection, array("id" => "content-redirector-group-selection", "class" => "hidden"));
	}

	echo elgg_view("input/button", array("id" => "content-redirector-selector-add", "value" => elgg_echo("content_redirector:selector:add"), "class" => "elgg-button-submit hidden"));
	?>
</div>

<script type="text/javascript">
	<?php
		foreach($supported_plugins as $plugin_name => $plugin_details){
			echo "var " . $plugin_name . "_details = new Array('" . $plugin_details["user_link"] . "', '" . $plugin_details["group_link"] . "'); 
				";
		}
	?>
</script>
	<?php

} else {
	echo elgg_echo("no supported content types available");
}
