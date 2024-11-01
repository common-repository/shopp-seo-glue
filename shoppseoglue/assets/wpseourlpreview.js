$j = jQuery.noConflict();

$j(document).ready(function(){
	/* Do not proceed unless we're confident that WP SEO's
	 * scripts and globals are present and initialized.
	 */
	if (typeof(boldKeywords) !== "function" ||
		typeof(testFocusKw) !== "function" ||
		typeof(wpseo_permalink_template) !== "string")
			return;

	/* WP SEO looks for #editable-post-name-full to form
	 * the URL in the snippet preview - the equivalent
	 * in the Shopp product editor is #editable-slug-full.
	 */
	var name = $j("#editable-slug-full").text();
	var url	= wpseo_permalink_template.replace("%postname%", name).replace("http://","");
	url = boldKeywords(url, true);
	jQuery("#wpseosnippet .url").html(url);
	testFocusKw();
});