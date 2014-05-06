<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function d7td_node_recent_content($variables) {
  $node = $variables['node'];
  $account = user_load($node->uid);
  $output = '<div class="node-title">';
  $output .= l($node->title, 'node/' . $node->nid) ;
  $output .= theme('mark', array('type' => node_mark($node->nid, $node->changed)));
  $output .= '</div><div class="node-author">';
  $output .= 'By ' .theme('username', array('account' => $account));
  $output .= "</div>";
  $output .= '<div class="node-created"><em>';
  $output .= format_date($node->created,'short');
  $output .= '</em></div>';

  return $output;
}
function d7td_node_recent_block($variables) {
  $rows = array();
  $output = '';

  $l_options = array('query' => drupal_get_destination());
  foreach ($variables['nodes'] as $node) {
    $row = array();
    $row[] = array(
      'data' => theme('node_recent_content', array('node' => $node)),
      'class' => 'title-author',
        
    );
    $row[] = array(
      'data' => node_access('update', $node) ? l(t('edit'), 'node/' . $node->nid . '/edit', $l_options) : '',
      'class' => 'edit',
    );
   /* $row[] = array(
      'data' => node_access('delete', $node) ? l(t('delete'), 'node/' . $node->nid . '/delete', $l_options) : '',
      'class' => 'delete',
    );*/
    $rows[] = $row;
  }

  if ($rows) {
    $output = theme('table', array('rows' => $rows));
    if (user_access('access content overview')) {
      $output .= theme('more_link', array('url' => 'admin/content', 'title' => t('Show more content')));
    }
  }

  return $output;
}

function d7td_mark($variables) {
  $type = $variables['type'];
  global $user;
  if ($user->uid) {
    if ($type == MARK_NEW) {
      return ' <span class="marker">New</span>';
    }
    elseif ($type == MARK_UPDATED) {
      return ' <span class="marker">Updated</span>';
    }
  }
}

function d7td_preprocess_username(&$variables) {
  if (!empty ($variables['account'] ->mail)) {
      $variables['extra'].=' (' .$variables['account']->mail .')';
  }
  $variables['link_attributes']['rel'] = 'nofollow';
}

function d7td_process_username(&$variables) {
 $variables['extra']= str_replace('@','@NOSPAM.',$variables['extra']);
}


function d7td_preprocess_node(&$variables) {
     $node = $variables['node'];
    if (variable_get('node_submitted_' . $node->type, TRUE)) {
    $variables['submitted'] = t('Posted by !username on !datetime', array('!username' => $variables['name'], '!datetime' => $variables['date']));
  }

}
function d7td_preprocess_html(&$variables) {
    if ($GLOBALS['user']->uid == 1) {
        drupal_add_css(drupal_get_path('theme', 'd7td') .'/css/superadmin.css');
    }
}

function d7td_form_alter(&$form, &$form_state, $form_id) {
   
  if (!empty($form['#node_edit_form'])) {
   
    unset($form['additional_settings']);
    $form['options']['#collapsed'] = FALSE;
    $form['menu']['#collapsed'] = FALSE;
    $form['path']['#collapsed'] = FALSE;
    if ($GLOBALS['user']->uid != 1) {
    $form['comment_settings']['#access'] = FALSE;
    }
 
  }
}

function d7td_theme($existing, $type, $theme, $path) {
    return array (
        'node_form' => array (
            'render element' => 'form',
            'template' => 'node-form',
            'path' => drupal_get_path('theme','d7td') .'/templates',
            
        )
    );
}

function d7td_preprocess_node_form(&$variables) {
    $variables['buttons']=drupal_render($variables['form']['actions']);
     if (!empty($variables['form']['field_tags'])) {
    $variables['tags'] = drupal_render($variables['form']['field_tags']);
  }

      $variables['right_side'] = drupal_render($variables['form']['options']);
  $variables['right_side'] .= drupal_render($variables['form']['path']);
  $variables['right_side'] .= drupal_render($variables['form']['menu']);
  
  $variables['right_side'] .= drupal_render($variables['form']['comment_settings']);
  $variables['right_side'] .= drupal_render($variables['form']['revision_information']);
  $variables['right_side'] .= drupal_render($variables['form']['author']);
    $variables['left_side']= drupal_render_children($variables['form']);
    drupal_add_css(drupal_get_path('theme', 'd7td') . '/css/node-form.css');
}
?>