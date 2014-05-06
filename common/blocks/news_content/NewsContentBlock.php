<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class NewsContentBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'news_content';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    //HTML attribute
    public $html;

    public function setParams($params) {
        $this->html = isset($params['html']) ? $params['html'] : '';
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            //Set Params from Block Params
            $params = b64_unserialize($this->block['params']);
            $this->setParams($params);
            if (isset($this->page)) {
                $connection = Yii::app()->db;

                $command = $connection->createCommand('SELECT * 
                FROM {{object}} obj
                JOIN {{object_meta}} objmeta ON objmeta.meta_object_id = obj.object_id
                WHERE gxc_object.object_id =:paramId');
                $command->bindValue(':paramId', $this->page['page_id'], PDO::PARAM_INT);
                $blocks = $command->queryAll();
                if ($blocks !== false) {
                    //Yii::app()->cache->set('pb-'.$this->page['page_id'].'-'.$this->region,$blocks,1800);						
                    $this->workWithBlocks($blocks);
                } else {
                    echo '';
                }
                /* Disable Cache Here
                  $blocks=Yii::app()->cache->get('pb-'.$this->page['page_id'].'-'.$this->region);
                  if($blocks===false){
                  } else {
                  $this->workWithBlocks($blocks);
                  }
                 */
            }
        } else {
            echo '';
        }
    }

    public function workWithBlocks($blocks) {
        foreach ($blocks as $block) {
            $this->blockRender($block);
        }
    }

    public function blockRender($block) {
        $block_ini = parse_ini_file(Yii::getPathOfAlias('common.blocks.' . $block['type']) . DIRECTORY_SEPARATOR . 'info.ini');
        //Include the class      

        Yii::import('common.blocks.' . $block['type'] . '.' . $block_ini['class']);
        $this->widget('common.blocks.' . $block['type'] . '.' . $block_ini['class'], array('block' => $block, 'page' => $this->page, 'layout_asset' => $this->layout_asset));
    }

    public static function setRenderOutput($obj) {
        // We will render the layout based on the 
        // layout                
        $name = (strpos($obj->id, '.') === false) ? $obj->id : substr($obj->id, strrpos($obj->id, '.') + 1);
        $render = 'common.blocks.' . $obj->id . '.' . $name . '_block_output';
        /* Delete for optimize		
          if(file_exists(Yii::getPathOfAlias('common.front_layouts.'.$obj->page->layout.'.blocks').'/'.$obj->id.'_block_output.php')){
          $render='common.front_layouts.'.$obj->page->layout.'.blocks.'.$obj->id.'_block_output';
          }
          Delete for optimize */
        return $render;
    }

    public function validate() {
        if ($this->html == "") {
            $this->errors['html'] = t('site', 'HTML content is required');
            return false;
        } else
            return true;
    }

    public function params() {
        return array(
            'html' => t('site', 'Html Content'),
        );
    }

    public function beforeBlockSave() {
        return true;
    }

    public function afterBlockSave() {
        return true;
    }

}
