<?php

if( !defined( 'ABSPATH' ) )  exit; // Exit if accessed directly.

/**
 * get all template content and file names
 *
 *
 * @param array $template_paths path of the templates
 * @param string $format format of the templates files without dot(.)
 * @return array multidimensional
 */
function quantum_addons_get_templates( $template_paths, $format )  {
   $templates = [];

   if( !is_array( $template_paths ) ) return $templates;

   foreach( $template_paths as $path )  {
      ///template folder exist and readable
      if( file_exists( $path ) && is_dir( $path ) && is_readable( $path ) )  {

         ///select only specified file
         $template_files = array_filter( glob( $path."/*.$format*" ), 'is_file' );

         foreach( $template_files as $file )  {
            if( !is_readable( $file ) )  continue;

            $base_name = basename( $file, "." . $format );
            ///replace underscore(_) and spaces with dash(-)
            $file_name = trim( preg_replace( '/[_\s]/', '-', $base_name ) );

            while( key_exists( $file_name, $templates ) )  {
               $i = 2;
               $underscore_position = strpos( $file_name, "_" );
               if( $underscore_position )  {
                  $file_name = substr( $file_name, 0, $underscore_position );
               }

               $file_name += "_" . $i++;
            }

            $templates[$file_name] = quantum_addons_remove_html_comments( file_get_contents( $file ) );
         }
      }
   }

   return $templates;
}

function quantum_addons_remove_html_comments( $content = '' )  {
   return preg_replace( '/<!--(.|\s)*?-->/', '', $content );
}

/**
 * @param string $template html template
 * @param array $template_tags needs key of the $content and value of regex without backward slashes (/.../) to find template tags
 * if $content has multidimensional array values you can add '=>' to the key eg: (image=>url) currently only support only one level
 * @param array $content array with key as same as the $template_tags so that tag(s) will be replaced
 * @return string of parsed template
 */
function quantum_addons_parse_template( string $template, array $template_tags, array $content )  {
   foreach( $template_tags as $key => $regex )  {
      $template_content = null;

      if( strpos( $key, '=>' ) )  {
         $multi_array_key = explode( '=>', $key );

         if( !isset( $content[$multi_array_key[0]][$multi_array_key[1]] ) )  continue;

         $template_content = $content[$multi_array_key[0]][$multi_array_key[1]];
      } else {
         if( !isset( $content[$key] ) ) continue;

         $template_content = $content[$key];
      }

      if( !is_string( $template_content ) ) continue;

      $template_content = trim( $template_content );

      if( $template_content !== '' ) {
         $template = preg_replace( str_replace( "REGEX", $regex, "/{{REGEX}}/" ), $template_content, $template );
      }

      // finally remove any other unused tags
      $template = preg_replace( str_replace( "REGEX", $regex, "/{{REGEX}}/" ), '', $template );
   }

   return $template;
}

/**
 * @param string $content from which ancher tags to be removed
 * @return string without ancher tags
 */
function quantum_addons_remove_ancher_tags( string $content )  {
   // remove ancher opening tag
   $content = preg_replace( '/<[\s]*a[^>]*>/', '', $content );
   // remove ancher closing tag
   $content = preg_replace( '/<[\s]*\/[\s]*a[\s]*[\s]*>/', '', $content );

   return $content;
}
