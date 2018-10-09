<?php
/**
 * @group plugin_datepicker
 * @group plugins
 */
class plugin_datepicker_ajax_test extends DokuWikiTest {

    public function setup() {
        $this->pluginsEnabled[] = 'datepicker';
        $this->pluginsEnabled[] = 'ajaxedit';
        parent::setup();
    }

    
    public function test_basic_ajax() {
        global $INFO;
        
        $INFO['id'] = 'test:plugin_datepicker:ajax';
        $INFO['perm'] = AUTH_EDIT;
        saveWikiText('test:plugin_datepicker:ajax',"<datepicker>",'test');
        
        $xhtml = p_wiki_xhtml('test:plugin_datepicker:ajax');
        $doc = phpQuery::newDocument($xhtml);
        
        $this->assertEquals(plugin_load('action', 'datepicker')->getConf('emptyStringDate'), pq("span.datepicker",$doc)->get(0)->textContent);
        
        $request = new TestRequest();
        $request->post([
            'call'      => 'plugin_datepicker', 
            'pageid'    => 'test:plugin_datepicker:ajax',
            'id'        => 0,
            'datestr'   => '2018-01-01',
            'mode'      => 'datepicker',
            
            'lastmod'   => @filemtime(wikiFN('test:plugin_datepicker:ajax')),
            
        ], '/lib/exe/ajax.php');
               
        $xhtml = p_wiki_xhtml('test:plugin_datepicker:ajax');
        $doc = phpQuery::newDocument($xhtml);

        $this->assertEquals('2018-01-01', pq("span.datepicker",$doc)->get(0)->textContent);
    }
    
    public function test_basic_ajax_weekpicker() {
        global $INFO;
        
        $INFO['id'] = 'test:plugin_datepicker:ajax';
        $INFO['perm'] = AUTH_EDIT;
        saveWikiText('test:plugin_datepicker:ajax', "<weekpicker><weekpicker 01/18>",'test');
        
        $request = new TestRequest();
        $request->post([
            'call'      => 'plugin_datepicker',
            'pageid'    => 'test:plugin_datepicker:ajax',
            'id'        => 1,
            'datestr'   => '02/18',
            'mode'      => 'weekpicker',
            
            'lastmod'   => @filemtime(wikiFN('test:plugin_datepicker:ajax')),
            
        ], '/lib/exe/ajax.php');
        
        $xhtml = p_wiki_xhtml('test:plugin_datepicker:ajax');
        $doc = phpQuery::newDocument($xhtml);
        
        $this->assertEquals(plugin_load('action', 'datepicker')->getConf('emptyStringWeek'), pq("span.weekpicker",$doc)->get(0)->textContent);
        $this->assertEquals('02/18', pq("span.weekpicker",$doc)->get(1)->textContent);
    }
}
