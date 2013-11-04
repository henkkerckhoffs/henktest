<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Category Model
 *
 * Model representation of category table(s).
 *
 * @package    Category Model
 * @author     Bas Botman
 * @version    1.0
 * @date       2013-09-23
*/

class Category_model extends BF_Model {

   protected $database = "";
   
   public function __construct()
   {
      ini_set('max_execution_time', 30);
      parent::__construct();
      $this->database = $this->load->database('db_backoffice', TRUE);
   }
   
   public function get_categories_henk()
   {
   	$this->database->select('*');
   	$query = $this->database->get('tbm_CTG_Categories');
   
   	if ($query->num_rows() > 0)
   	{
   		return $query->result();
   	}
   }
   
   public function get_categories_henk_bla()
   {
   	$this->database->select('*');
   	$query = $this->database->get('tbm_CTG_Categories');
   
   	if ($query->num_rows() > 0)
   	{
   		return $query->result();
   	}
   }
   
   public function get_categories()
   {
      $this->database->select('*');
      $query = $this->database->get('tbm_CTG_Categories');
      
      if ($query->num_rows() > 0)
      {
         return $query->result();
      }
   }
   
   public function retrieve_categories()
   {
      $this->database->select('*');
      $this->database->where('dat_CTG_DeleteDateTime IS NULL');
      $query = $this->database->get('tbm_CTG_Categories');
      
      if ($query->num_rows() > 0)
      {
         return $query->result();
      }
   }
   
   public function get_structure($categoryId)
   {
      $parentArray       =   array();
      $idArray           =   $this->get_parent($categoryId, $parentArray);
      
      return $idArray;
   }
   
   protected function get_parente($categoryId, $parentArray)
   {
      $this->database->select(            'aut_CTG_CategoryId, int_CTG_ParentCategoryId');
      $this->database->where(             'aut_CTG_CategoryId', $categoryId);
      $query = $this->database->get(      'tbm_CTG_Categories');
      
      if($query->num_rows() > 0)
      {
         $parentData                         =   $query->row_array();
         $parentData['categoryElements']     =   $this->category_elements($parentData['aut_CTG_CategoryId']);
         
         $parentArray[]                      =   $parentData;
         
         if($parentData['int_CTG_ParentCategoryId'] > 0)
         {
            return $this->get_parent($parentData['int_CTG_ParentCategoryId'], $parentArray);
         }
         else
         {
            $idArray                         =   array_reverse($parentArray);
            return $idArray;
         }
      }
   }
   
   public function check_product_group($elementId)
   {
      $this->database->select(            'int_SEL_ElementId_ELM, int_SEL_ProductGroupId_PRG');
      $this->database->where(             'int_SEL_ElementId_ELM', $elementId);
      $this->database->where(             'int_SEL_ProductGroupId_PRG IS NOT NULL');
      $this->database->where(             'dat_SEL_DeleteDateTime IS NULL');
      $query = $this->database->get(      'tbt_SEL_SelectedElements');
       
      if ($query->num_rows() > 0)
      {
         return $query->result_array();
      }
   }
   
   public function check_product_group_henk($elementId)
   {
      $this->database->select(            'int_SEL_ElementId_ELM, int_SEL_ProductGroupId_PRG');
      $this->database->where(             'int_SEL_ElementId_ELM', $elementId);
      $this->database->where(             'int_SEL_ProductGroupId_PRG IS NOT NULL');
      $this->database->where(             'dat_SEL_DeleteDateTime IS NULL');
      $query = $this->database->get(      'tbt_SEL_SelectedElements');
       
      if ($query->num_rows() > 0)
      {
         return $query->result_array();
      }
   }
   
   public function element_structure($elementId, $elementStructureArray)
   {
      $this->database->select(            'aut_ELM_ElementId, int_ELM_ParentElementId, str_ELM_Name');
      $this->database->where(             'int_ELM_ParentElementId', $elementId);
      $this->database->where(             'dat_ELM_DeleteDateTime IS NULL');
      $query = $this->database->get(      'tbm_ELM_Elements');
      
      if($query->num_rows() > 0)
      {
         $childData                      =   $query->result_array();
         
         foreach($childData as $key => $singleChild)
         {
            $elementStructureArray[]     =   $singleChild;
            $newChildData                =   $this->element_structure($singleChild['aut_ELM_ElementId'], $elementStructureArray);
            
            if(sizeof($newChildData) > 0)
            {
               foreach($newChildData as $key2 => $single_Child)
               {
                  $elementStructureArray[] = $single_Child;
               }
            }
         }
         
         return $elementStructureArray;
      }
   }

   public function category_element($elementId)
   {
      $this->database->select(            'aut_ELM_ElementId, int_ELM_ParentElementId, str_ELM_Name');
      $this->database->where(             'aut_ELM_ElementId', $elementId);
      $query = $this->database->get(      'tbm_ELM_Elements');
      
      if ($query->num_rows() > 0)
      {
         return $query->row_array();
      }
   }
   
   public function category_elements($categoryId)
   {
      $this->database->select(            'int_CAE_ElementId_ELM');
      $this->database->where(             'int_CAE_CategoryId_CTG', $categoryId);
      
      $query = $this->database->get(      'tbt_CAE_CategoriesElements');
      
      if ($query->num_rows() > 0)
      {
         return $query->result_array();
      }
   }
   
   public function create_category_element($insertArray)
   {
      $query = $this->database->insert('tbt_CAE_CategoriesElements', $insertArray);
      
      return $query;
   }
   
   public function create_category_element_demo($insertArray)
   {
      $query = $this->database->insert('tbt_CAE_CategoriesElements', $insertArray);
      
      return $query;
   }
   
   public function match_category_element($categoryId,$elementId)
   {
      $this->database->select('*');
      $this->database->where('int_CAE_CategoryId_CTG', $categoryId);
      $this->database->where('int_CAE_ElementId_ELM', $elementId);
      $query = $this->database->get('tbt_CAE_CategoriesElements');
      
      if ($query->num_rows() > 0)
      {
         return $query->result_array();
      }
   }
}
