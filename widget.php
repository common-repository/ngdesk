
<script type="text/javascript">
	
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = 'https://<?php echo get_option( "subdomain" )?>.ngdesk.com/widgets/chat/<?php echo get_option( "widgetid" );?>/chat_widget.js';
	document.getElementsByTagName("head")[0].appendChild(script);	

</script>
