<?php
/**
 * @group plugin_datepicker
 * @group plugins
 */
class plugin_datepicker_syntax_test extends DokuWikiTest {

    protected $plugin;
    public function setup() {
        $this->pluginsEnabled[] = 'datepicker';
        $this->pluginsEnabled[] = 'ajaxedit';
        parent::setup();
        $this->plugin = plugin_load('action', 'datepicker'); 
    }

    
    public function test_basic_datepicker_empty_syntax() {
        global $INFO;
        $id = 'test:plugin_datepicker:syntax';
        $INFO['id'] = $id;
        saveWikiText($id,
            '<datepicker>'.DOKU_LF.
            '<datepicker#>'.DOKU_LF.
            '<datepicker\\>'.DOKU_LF
            ,'test');
        
        $xhtml = p_wiki_xhtml($id);
        $doc = phpQuery::newDocument($xhtml);
        
        $mselector = pq("span.datepicker",$doc);
        $this->assertTrue($mselector->length === 0);
        $mselector = pq("p",$doc);
        $this->assertTrue($mselector->length === 1);
        $this->assertEquals(
            $this->plugin->getConf('emptyStringDate').DOKU_LF.
            $this->plugin->getConf('emptyStringDate').DOKU_LF.
            $this->plugin->getConf('emptyStringDate'),trim($mselector->eq(0)->text()));
        
        
        $INFO['perm'] = AUTH_EDIT;
        $xhtml = p_wiki_xhtml($id);
        $doc = phpQuery::newDocument($xhtml);
        
        $mselector = pq("span.datepicker",$doc);
        $this->assertTrue($mselector->length === 3);
        $this->assertEquals($this->plugin->getConf('emptyStringDate'), trim($mselector->eq(0)->text()));
        $this->assertEquals($this->plugin->getConf('emptyStringDate'), trim($mselector->eq(1)->text()));
        $this->assertEquals($this->plugin->getConf('emptyStringDate'), trim($mselector->eq(2)->text()));
    }
    
    public function test_basic_weekpicker_empty_syntax() {
        global $INFO;
        $id = 'test:plugin_datepicker:syntax2';
        $INFO['id'] = $id;
        saveWikiText($id,
            '<weekpicker>'.DOKU_LF.
            '<weekpicker#>'.DOKU_LF.
            '<weekpicker\\>'.DOKU_LF
            ,'test');
        
        $xhtml = p_wiki_xhtml($id);
        $doc = phpQuery::newDocument($xhtml);
        
        $mselector = pq("span.weekpicker",$doc);
        $this->assertTrue($mselector->length === 0);
        $mselector = pq("p",$doc);
        $this->assertTrue($mselector->length === 1);
        $this->assertEquals(
            $this->plugin->getConf('emptyStringWeek').DOKU_LF.
            $this->plugin->getConf('emptyStringWeek').DOKU_LF.
            $this->plugin->getConf('emptyStringWeek'),trim($mselector->eq(0)->text()));
        
        $INFO['perm'] = AUTH_EDIT;
        $xhtml = p_wiki_xhtml($id);
        $doc = phpQuery::newDocument($xhtml);
        
        $mselector = pq("span.weekpicker",$doc);
        $this->assertTrue($mselector->length === 3);
        $this->assertEquals($this->plugin->getConf('emptyStringWeek'), trim($mselector->eq(0)->text()));
        $this->assertEquals($this->plugin->getConf('emptyStringWeek'), trim($mselector->eq(1)->text()));
        $this->assertEquals($this->plugin->getConf('emptyStringWeek'), trim($mselector->eq(2)->text()));
    }
    public function test_basic_datepicker_syntax() {
        global $INFO;
        $INFO['perm'] = AUTH_EDIT;
        $id = 'test:plugin_datepicker:syntax3';
        $INFO['id'] = $id;
        saveWikiText($id,
            '<datepicker 2014-02-01>'.DOKU_LF.
            '<datepicker# 2014-02-01>'.DOKU_LF.
            '<datepicker\\ 2014-02-01>'.DOKU_LF
            ,'test');
        
        $xhtml = p_wiki_xhtml($id);
        $doc = phpQuery::newDocument($xhtml);
        
        $mselector = pq("span.datepicker",$doc);
        $this->assertTrue($mselector->length === 3);
        $this->assertEquals('2014-02-01',trim($mselector->eq(0)->text()));
        $this->assertEquals('2014-02-01',trim($mselector->eq(1)->text()));
        $this->assertEquals('2014-02-01',trim($mselector->eq(2)->text()));
    }
    
    public function test_basic_weekpicker_syntax() {
        global $INFO;
        $INFO['perm'] = AUTH_EDIT;
        $id = 'test:plugin_datepicker:syntax4';
        $INFO['id'] = $id;
        saveWikiText($id,
            '<weekpicker 14/02>'.DOKU_LF.
            '<weekpicker# 14/02>'.DOKU_LF.
            '<weekpicker\\ 14/02>'.DOKU_LF
            ,'test');
        
        $xhtml = p_wiki_xhtml($id);
        $doc = phpQuery::newDocument($xhtml);
        
        $mselector = pq("span.weekpicker",$doc);
        $this->assertTrue($mselector->length === 3);
        $this->assertEquals('14/02',trim($mselector->eq(0)->text()));
        $this->assertEquals('14/02',trim($mselector->eq(1)->text()));
        $this->assertEquals('14/02',trim($mselector->eq(2)->text()));
    }

}
