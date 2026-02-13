<!-- 9. WEEKLY EGG SALES -->
<div class="record-section">
    <div class="section-header">9. Weekly Egg Sales 🥚</div>
    <div class="section-body">
        <div class="dashboard-grid">
            <div class="form-group">
                <label>Grade 1 Crates Sold</label>
                <input type="number" step="0.1" name="grade1_sold" value="<?php echo isset($sales['grade1_sold']) ? $sales['grade1_sold'] : ''; ?>" placeholder="0.0">
            </div>
            <div class="form-group">
                <label>Grade 2 Crates Sold</label>
                <input type="number" step="0.1" name="grade2_sold" value="<?php echo isset($sales['grade2_sold']) ? $sales['grade2_sold'] : ''; ?>" placeholder="0.0">
            </div>
            <div class="form-group">
                <label>Grade 3 Crates Sold</label>
                <input type="number" step="0.1" name="grade3_sold" value="<?php echo isset($sales['grade3_sold']) ? $sales['grade3_sold'] : ''; ?>" placeholder="0.0">
            </div>
            <div class="form-group">
                <label>Grade 4 Crates Sold</label>
                <input type="number" step="0.1" name="grade4_sold" value="<?php echo isset($sales['grade4_sold']) ? $sales['grade4_sold'] : ''; ?>" placeholder="0.0">
            </div>
            <div class="form-group">
                <label>Price per Crate</label>
                <input type="number" step="0.01" name="price_per_crate" value="<?php echo isset($sales['price_per_crate']) ? $sales['price_per_crate'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group" style="border-top: 1px solid #ccc; margin-top: 10px; padding-top: 10px;">
                <label>Cash Received</label>
                <input type="number" step="0.01" name="cash_received" value="<?php echo isset($sales['cash_received']) ? $sales['cash_received'] : ''; ?>" placeholder="0.00">
            </div>
             <div class="form-group" style="border-top: 1px solid #ccc; margin-top: 10px; padding-top: 10px;">
                <label>Credit Sales</label>
                <input type="number" step="0.01" name="credit_sales" value="<?php echo isset($sales['credit_sales']) ? $sales['credit_sales'] : ''; ?>" placeholder="0.00">
            </div>
             <div class="form-group" style="border-top: 1px solid #ccc; margin-top: 10px; padding-top: 10px;">
                <label>Outstanding Balances (Previous)</label>
                <input type="number" step="0.01" name="start_balance" value="<?php echo isset($sales['start_balance']) ? $sales['start_balance'] : ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group" style="border-top: 1px solid #ccc; margin-top: 10px; padding-top: 10px;">
                <label>Delivery / Transport Costs</label>
                <input type="number" step="0.01" name="transport_cost_sales" value="<?php echo isset($sales['transport_cost_sales']) ? $sales['transport_cost_sales'] : ''; ?>" placeholder="0.00">
            </div>
        </div>
    </div>
</div>
