<?php
/*
Plugin Name: Column Demo
Plugin URI: https://redoyit.com/
Description: Used by millions, WordCount is quite possibly the best way in the world to <strong>protect your blog from spam</strong>. WordCount Anti-spam keeps your site protected even while you sleep. To get started: activate the WordCount plugin and then go to your WordCount Settings page to set up your API key.
Version: 5.3
Requires at least: 5.8
Requires PHP: 5.6.20
Author: Md. Redoy Islam
Author URI: https://redoyit.com/wordpress-plugins/
License: GPLv2 or later
Text Domain: columndemo
Domain Path: /languages
*/

class ColumnDemo{

    function __construct(){
        add_action('plugin_loaded', array($this, 'coldemo_load_textdomain'));
    }
    function coldemo_load_textdomain(){ 
        load_plugin_textdomain('columndemo', false, dirname(__FILE__) . '/languages');
    }
}
new ColumnDemo();
//Posts Custom Colum
function coldemo_post_columns( $columns ){
    print_r($columns);
    unset($columns['tags']);
    unset($columns['comments']);
    /*unset($columns['author']);
    unset($columns['date']);
    $columns['author']='Author';
    $columns['date']='Date';
    */
    $columns['id'] = __('Post ID', 'columndemo');
    $columns['thumbnail'] = __('Thumbnail', 'columndemo');
    $columns['wordcount'] = __('Word Count', 'columndemo');

    return $columns;
}
add_filter('manage_posts_columns', 'coldemo_post_columns');
function coldemo_post_column_data($column, $post_id){
    if('id' == $column){
        echo $post_id;
    }elseif('thumbnail' == $column){
        $thumbnail = get_the_post_thumbnail($post_id, array(100,100));
        echo $thumbnail;
    }elseif('wordcount' == $column){
        /*$_post = get_post($post_id);
        $content = $_post->post_content;
        $wordn = str_word_count(strip_tags($content));*/
        $wordn = get_post_meta($post_id, 'wordn', true);
        echo $wordn; 
    }
}
add_action('manage_posts_custom_column', 'coldemo_post_column_data',10,2);

function coldemo_sortable_column($columns){
    $columns['wordcount']='wordn';
    return $columns;
}
add_filter('manage_edit-post_sortable_columns', 'coldemo_sortable_column');
/*
function coldemo_set_word_count(){
    $_posts = get_posts(array(
        'post_per_page' => -1,
        'post_type'=>'post',
    ));
    foreach($_posts as $p){
        $content = $p->post_content;
        $wordn = str_word_count(strip_tags($content));
        update_post_meta($p->ID, 'wordn', $wordn);
    }
}
add_action('init', 'coldemo_set_word_count');
*/
function coldemo_sort_column_data($wpquery){
    if(!is_admin()){
        return;
    }
    $orderby = $wpquery->get('orderby');
    if('wordn' == $orderby){
        $wpquery->set('meta_key','wordn');
        $wpquery->set('orderby','meta_value_num');
    }
}
add_action('pre_get_posts', 'coldemo_sort_column_data');

function coldemo_update_wordcount_on_post_save($post_id){
    $p = get_post($post_id);
    $content = $p->post_content;
    $wordn = str_word_count(strip_tags($content));
    update_post_meta($p->ID, 'wordn', $wordn);
}
add_action('save_post', 'coldemo_update_wordcount_on_post_save');

//Page Custom Column Add
function coldemo_pages_columns( $columns ){
    print_r($columns);
    unset($columns['tags']);
    unset($columns['comments']);
    /*unset($columns['author']);
    unset($columns['date']);
    $columns['author']='Author';
    $columns['date']='Date';
    */
    $columns['id'] = __('Post ID', 'columndemo');
    $columns['thumbnail'] = __('Thumbnail', 'columndemo');

    return $columns;
}
add_filter('manage_pages_columns', 'coldemo_pages_columns');
function coldemo_pages_column_data($column, $post_id){
    if('id' == $column){
        echo $post_id;
    }elseif('thumbnail' == $column){
        $thumbnail = get_the_post_thumbnail($post_id, array(100,100));
        echo $thumbnail;
    }
}
add_action('manage_pages_custom_column', 'coldemo_pages_column_data',10,2);



//Post Filter Create

function coldemo_filter(){
    if(isset($_GET['post_type']) && $_GET['post_type'] != 'post'){
        return;
    }
    $filter_value = isset($_GET['DEMOFILTER'])?$_GET['DEMOFILTER']:'';
    $values = array(
        '0' => __('Select Status', 'columndemo'),
        '1' => __('Some Post', 'columndemo'),
        '2' => __('Some Post++', 'columndemo'),
    )
    ?>
    <select name="DEMOFILTER">
    <?php 
        foreach($values as $key => $value){
            printf("<option value='%s' %s>%s</option>",$key, $key == $filter_value?"selected='selected'":'',$value);
        }
    ?>
    </select> 
    <?php
}
add_action('restrict_manage_posts', 'coldemo_filter');

function coldemo_filter_data($wpquery){
    if(!is_admin()){
        return;
    }
    $filter_value = isset($_GET['DEMOFILTER'])?$_GET['DEMOFILTER']:'';
    if('1' == $filter_value){
        $wpquery->set('post__in', array(29,27,25));
    }elseif('2' == $filter_value){
        $wpquery->set('post__in', array(23,1));
    }
}
add_action('pre_get_posts', 'coldemo_filter_data');

//Post Thumbnail Filter
function coldemo_thumbnail_filter(){
    if(isset($_GET['post_type']) && $_GET['post_type'] != 'post'){
        return;
    }
    $filter_value = isset($_GET['THFILTER'])?$_GET['THFILTER']:'';
    $values = array(
        '0' => __('Thumbnail Status', 'columndemo'),
        '1' => __('Has Thumbnail', 'columndemo'),
        '2' => __('No Thumbnail', 'columndemo'),
    )
    ?>
    <select name="THFILTER">
    <?php 
        foreach($values as $key => $value){
            printf("<option value='%s' %s>%s</option>",$key, $key == $filter_value?"selected='selected'":'',$value);
        }
    ?>
    </select> 
    <?php
}
add_action('restrict_manage_posts', 'coldemo_thumbnail_filter');

function coldemo_thumbnail_filter_data($wpquery){
    if(!is_admin()){
        return;
    }
    //$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $filter_value = isset($_GET['THFILTER'])?$_GET['THFILTER']:'';
    //$wpquery->set('paged', $paged);
    if('1' == $filter_value){
        $wpquery->set('meta_query', array(
            array(
                'key'=>'_thumbnail_id',
                'compare' => 'EXISTS',
            )
        ));
    }elseif('2' == $filter_value){
        $wpquery->set('meta_query', array(
            array(
                'key'=>'_thumbnail_id',
                'compare' => 'NOT EXISTS',
            )
        ));
    }
}
add_action('pre_get_posts', 'coldemo_thumbnail_filter_data');

//Post Count Meta Filter
function coldemo_wc_filter(){
    if(isset($_GET['post_type']) && $_GET['post_type'] != 'post'){
        return;
    }
    $filter_value = isset($_GET['WCFILTER'])?$_GET['WCFILTER']:'';
    $values = array(
        '0' => __('Word Count', 'columndemo'),
        '1' => __('Above 400', 'columndemo'),
        '2' => __('200 to 400', 'columndemo'),
        '3' => __('Below 200', 'columndemo'),
    )
    ?>
    <select name="WCFILTER">
    <?php 
        foreach($values as $key => $value){
            printf("<option value='%s' %s>%s</option>",$key, $key == $filter_value?"selected='selected'":'',$value);
        }
    ?>
    </select> 
    <?php
}
add_action('restrict_manage_posts', 'coldemo_wc_filter');

function coldemo_wc_filter_data($wpquery){
    if(!is_admin()){
        return;
    }
    //$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $filter_value = isset($_GET['WCFILTER'])?$_GET['WCFILTER']:'';
    //$wpquery->set('paged', $paged);

    if('1' == $filter_value){
        $wpquery->set('meta_query', array(
            array(
                'key'=>'wordn',
                'value' => 400,
                'compare' => '>=',
                'type' => 'NUMERIC',
            )
        ));
    }elseif('2' == $filter_value){
        $wpquery->set('meta_query', array(
            array(
                'key'=>'wordn',
                'value' => array(200,400),
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC',
            )
        ));
    }elseif('3' == $filter_value){
        $wpquery->set('meta_query', array(
            array(
                'key'=>'wordn',
                'value' => 200,
                'compare' => '<=',
                'type' => 'NUMERIC',
            )
        ));
    }
}
add_action('pre_get_posts', 'coldemo_wc_filter_data');
