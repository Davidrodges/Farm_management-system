<!-- 13. WEEKLY MANAGER NOTES -->
<div class="record-section">
    <div class="section-header">13. Weekly Manager Notes 📅</div>
    <div class="section-body">
        <div class="dashboard-grid">
            <div class="form-group" style="grid-column: span 2;">
                <label>Major Challenges</label>
                <textarea name="challenges"><?php echo isset($notes['challenges']) ? $notes['challenges'] : ''; ?></textarea>
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Disease or Mortality Concerns</label>
                <textarea name="concerns"><?php echo isset($notes['concerns']) ? $notes['concerns'] : ''; ?></textarea>
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Feed or Supplier Issues</label>
                <textarea name="supplier_issues"><?php echo isset($notes['supplier_issues']) ? $notes['supplier_issues'] : ''; ?></textarea>
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Market Price Changes</label>
                <textarea name="market_changes"><?php echo isset($notes['market_changes']) ? $notes['market_changes'] : ''; ?></textarea>
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Action Plan Next Week</label>
                <textarea name="action_plan"><?php echo isset($notes['action_plan']) ? $notes['action_plan'] : ''; ?></textarea>
            </div>
        </div>
    </div>
</div>
