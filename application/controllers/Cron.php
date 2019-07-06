<?php

    class Cron extends CI_Controller{

        public function __construct(){
            parent::__construct();
        }

        //--- VOID transactions that are 7 days old + have not been used
        public function checkToVoidTransactions(){

            /*
             *
             * Zero out member_balance
             * Add refund transaction to transactions table
             * Email user
             *
             * */

            $getTransactions = $this->db->query("

                SELECT
                    transactions.*

                FROM transactions

                WHERE
                    transactions.type = 'purchase' AND
                    transactions.payment_type = 'cc' AND
                    transactions.datetime <= DATE_SUB(NOW(), INTERVAL 7 DAY) AND
                    transactions.time_used = 0 AND
                    transactions.settled IS NULL

            ");

            if($getTransactions->num_rows() > 0){

                $this->load->model('member_billing');
                foreach($getTransactions->result() as $t){
                    $this->member_billing->void_transaction($t->id);
                }

            }

        }

    }