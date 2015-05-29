<?php

namespace xMLM;

class Grid_CreditReportSum extends \Grid {
	public $amount_fields=array();
	public $from_date=array();
	public $to_date=array();

	function updateGrandTotals()
    {
        // get model
        $m = clone $this->getIterator();
        // create DSQL query for sum and count request
        $fields = array_keys($this->totals);

        // select as sub-query
        $sub_q = $m->_dsql()->del('limit')->del('order')->del('group')->del('having');

        // $q = $this->api->db->dsql();//->debug();
        // $q->table($sub_q->render(), 'grandTotals'); // alias is mandatory if you pass table as DSQL
        $sub_q->field($sub_q->count(), 'total_cnt');

        // execute DSQL
        $data = $sub_q->getHash();

        foreach ($fields as $field) {
            $data[$field] = $m->sum($field)->getOne();
        }
        $data['username']="Total";
        // parse results
        $this->total_rows = $data['total_cnt'];
        unset($data['total_cnt']);
        $this->totals = $data;
    }

}