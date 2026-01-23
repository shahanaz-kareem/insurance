# PowerShell script to convert action() to route() in blade templates
$replacements = @{
    "action\('ClientController@delete'," = "route('clients.delete',"
    "action\('ClientController@getOne'," = "route('clients.one',"
    "action\('ClientController@getAll'\)" = "route('clients.all')"
    "action\('ClientController@edit'," = "route('clients.edit',"
    "action\('PolicyController@delete'," = "route('policies.delete',"
    "action\('PolicyController@getOne'," = "route('policies.one',"
    "action\('PolicyController@getAll'\)" = "route('policies.all')"
    "action\('PolicyController@add'\)" = "route('policies.add')"
    "action\('PolicyController@edit'," = "route('policies.edit',"
    "action\('NoteController@delete'," = "route('notes.delete',"
    "action\('NoteController@add'\)" = "route('notes.add')"
    "action\('AttachmentController@delete'," = "route('attachments.delete',"
    "action\('AttachmentController@add'\)" = "route('attachments.add')"
    "action\('EmailController@getAll'," = "route('emails.all',"
    "action\('EmailController@add'\)" = "route('emails.add')"
    "action\('EmailController@delete'," = "route('emails.delete',"
    "action\('TextController@getAll'," = "route('texts.all',"
    "action\('TextController@add'\)" = "route('texts.add')"
    "action\('TextController@delete'," = "route('texts.delete',"
    "action\('PaymentController@delete'," = "route('payments.delete',"
    "action\('PaymentController@add'\)" = "route('payments.add')"
    "action\('MutualfundController@delete'," = "route('mutualfunds.delete',"
    "action\('MutualfundController@getAll'\)" = "route('mutualfunds.all')"
    "action\('MutualfundController@add'\)" = "route('mutualfunds.add')"
    "action\('MutualfundController@edit'," = "route('mutualfunds.edit',"
    "action\('MproductController@getAll'\)" = "route('mproducts.all')"
    "action\('MproductController@delete'," = "route('mproducts.delete',"
    "action\('MproductController@add'\)" = "route('mproducts.add')"
    "action\('MproductController@edit'," = "route('mproducts.edit',"
    "action\('BrokerController@delete'," = "route('brokers.delete',"
    "action\('BrokerController@getOne'," = "route('brokers.one',"
    "action\('BrokerController@getAll'\)" = "route('brokers.all')"
    "action\('BrokerController@add'\)" = "route('brokers.add')"
    "action\('BrokerController@edit'," = "route('brokers.edit',"
    "action\('BranchController@getOne'," = "route('branches.one',"
    "action\('BranchController@getAll'\)" = "route('branches.all')"
    "action\('BranchController@delete'," = "route('branches.delete',"
    "action\('StaffController@delete'," = "route('staff.delete',"
    "action\('StaffController@getOne'," = "route('staff.one',"
    "action\('StaffController@getAll'\)" = "route('staff.all')"
    "action\('StaffController@add'\)" = "route('staff.add')"
    "action\('StaffController@edit'," = "route('staff.edit',"
    "action\('CommissionController@delete'," = "route('commission.delete',"
    "action\('CommissionController@add'\)" = "route('commission.add')"
    "action\('CommissionController@edit'\)" = "route('commission.edit')"
    "action\('InboxController@getAll'," = "route('inbox.all',"
    "action\('ProductController@delete'," = "route('products.delete',"
    "action\('ProductController@getAll'\)" = "route('products.all')"
    "action\('ProductController@add'\)" = "route('products.add')"
    "action\('ProductController@edit'," = "route('products.edit',"
    "action\('ReportController@get'," = "route('reports.index',"
    "action\('ReportController@get'\)" = "route('reports.index')"
    "action\('SettingController@get'\)" = "route('settings.index')"
    "action\('SettingController@load'\)" = "route('settings.load')"
}

# Get all blade.php files
$files = Get-ChildItem -Path "resources\views" -Filter "*.blade.php" -Recurse

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $modified = $false
    
    foreach ($pattern in $replacements.Keys) {
        if ($content -match $pattern) {
            $content = $content -replace $pattern, $replacements[$pattern]
            $modified = $true
        }
    }
    
    if ($modified) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        Write-Host "Updated: $($file.FullName)"
    }
}

Write-Host "Conversion complete!"
