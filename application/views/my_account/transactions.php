
	<style>

		table td{ font-size:12px; }

	</style>
	
	<div style='padding-bottom:25px;'>
		<h2>Billing & Transaction History</h2>
	</div>
	
	<?
	
		if($transactions)
		{
		
			echo "<table width='100%' class='table table-striped table-hover' cellPadding='5' cellSpacing='0'>

			<thead>
                <tr>
                    <td>Date</td>
                    <td width='100'>Type</td>
                    <td>Summary</td>
                    <td>Amount</td>
                </tr>
            </thead>
            <tbody>";
		
			foreach($transactions as $t)
			{

                switch($t['type']){

                    case "earning":
                        $amountString = "+ $".number_format($t['amount'], 2);
                        $label = "Earning";
                        break;

                    case "payment":
                        $amountString = "- $".number_format($t['amount'], 2);
                        $label = "Payment";
                        break;

                    case "purchase":
                        $amountString = "- $".number_format($t['amount'], 2);
                        $label = "Purchase";
                        break;

                    case "consume":
                        $amountString = "- $".number_format($t['amount'], 2);
                        $label = "Chat Time Used";
                        $t['summary'] = "Used chat time or email credits";
                        break;

                    case "refund":
                        $amountString = "+ $".number_format($t['amount'] * -1, 2);
                        $label = "Refunded";
                        break;

                }

                echo "
                <tr>
                    <td width='100'>".date("m/d/Y", strtotime($t['datetime']))."</td>
                    <td width='75'>{$label}</td>
                    <td>{$t['summary']}</td>
                    <td width='100'>{$amountString}</td>
                </tr>
                ";
			
			}
			
			echo "</tbody>
			</table>";
		
		}
		else
		{
		
			echo "<p>There are no transactions</p>";
		
		}
	
	?>