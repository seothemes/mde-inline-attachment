<?php

/**
 * Plugin Name: Markdown Editor Inline Attachment
 * Plugin URI:  https://github.com/seothemes/markdown-editor
 * Description: Adds image paste functionality to Markdown Editor.
 * Version:     0.1.0
 * Author:      SEO Themes
 * Author URI:  https://www.seothemes.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: jetpack
 * Domain Path: /languages
 *
 * @package markdown-editor
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'admin_enqueue_scripts', 'mdeia_enqueue_scripts_styles' );
/**
 * Enqueue scripts and styles.
 *
 * @since 0.1.0
 * @return void
 */
function mdeia_enqueue_scripts_styles() {

	// Only enqueue on specified post types.
	if ( ! post_type_supports( get_current_screen()->post_type, 'wpcom-markdown' ) ) {
		return;
	}

	wp_enqueue_script( 'ia-s', plugin_dir_url( __FILE__ ) . 'inline-attachment.min.js' );
	wp_enqueue_script( 'cm4-s', plugin_dir_url( __FILE__ ) . 'codemirror-4.inline-attachment.min.js' );

}

add_action( 'admin_footer', 'mdeia_init_editor' );
/**
 * Inline Scripts.
 *
 * @since 1.0.0
 *
 * @return void
 */
function mdeia_init_editor() {

	// Only initialize on specified post types.
	if ( ! post_type_supports( get_current_screen()->post_type, 'wpcom-markdown' ) ) {
		return;
	}
	?>
    <script type="text/javascript">

        inlineAttachment.editors.codemirror4.attach(simplemde.codemirror, {
            onFileUploadResponse: function (xhr) {
                var result = JSON.parse(xhr.responseText),
                    filename = result[this.settings.jsonFieldName];
                console.log(result);
                console.log(filename);
                console.log(this.filenameTag);
                if (result && filename) {
                    var newValue;
                    if (typeof this.settings.urlText === 'function') {
                        newValue = this.settings.urlText.call(this, filename, result);
                    } else {
                        newValue = this.settings.urlText.replace(this.filenameTag, filename);
                    }
                    console.log(newValue);
                    var text = this.editor.getValue().replace(this.lastValue, newValue);
                    this.editor.setValue(text);
                    this.settings.onFileUploaded.call(this, filename);
                }
                return false;
            },
            uploadUrl: '/wp-content/plugins/mde-inline-attachment/upload-file.php',
            jsonFieldName: 'filename',
            urlText: "![Image]({filename})"
        });

    </script>
	<?php
}