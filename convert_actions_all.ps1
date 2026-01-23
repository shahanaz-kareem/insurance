# PowerShell script to convert action() calls to route() calls in Blade templates

$viewsPath = "c:\laragon\www\Iindia\resources\views"
$bladeFiles = Get-ChildItem -Path $viewsPath -Filter "*.blade.php" -Recurse

# Define the mapping of action() to route()
$mappings = @(
    @{ old = "action('ClientController@delete',"; new = "route('clients.delete'," },
    @{ old = "action('ClientController@getOne',"; new = "route('clients.one'," },
    @{ old = "action('ClientController@getAll')"; new = "route('clients.all')" },
    @{ old = "action('ClientController@edit',"; new = "route('clients.edit'," },
    @{ old = "action('PolicyController@delete',"; new = "route('policies.delete'," },
    @{ old = "action('PolicyController@getOne',"; new = "route('policies.one'," },
    @{ old = "action('PolicyController@getAll')"; new = "route('policies.all')" },
    @{ old = "action('PolicyController@add')"; new = "route('policies.add')" },
    @{ old = "action('PolicyController@edit',"; new = "route('policies.edit'," },
    @{ old = "action('NoteController@delete',"; new = "route('notes.delete'," },
    @{ old = "action('NoteController@add')"; new = "route('notes.add')" },
    @{ old = "action('AttachmentController@delete',"; new = "route('attachments.delete'," },
    @{ old = "action('AttachmentController@add')"; new = "route('attachments.add')" },
    @{ old = "action('EmailController@getAll',"; new = "route('emails.all'," },
    @{ old = "action('EmailController@add')"; new = "route('emails.add')" },
    @{ old = "action('TextController@getAll',"; new = "route('texts.all'," },
    @{ old = "action('TextController@add')"; new = "route('texts.add')" },
    @{ old = "action('TextController@delete',"; new = "route('texts.delete'," },
    @{ old = "action('PaymentController@delete',"; new = "route('payments.delete'," },
    @{ old = "action('PaymentController@add')"; new = "route('payments.add')" },
    @{ old = "action('MutualfundController@delete',"; new = "route('mutualfunds.delete'," },
    @{ old = "action('MutualfundController@getAll')"; new = "route('mutualfunds.all')" },
    @{ old = "action('MutualfundController@add')"; new = "route('mutualfunds.add')" },
    @{ old = "action('MutualfundController@edit',"; new = "route('mutualfunds.edit'," },
    @{ old = "action('MproductController@getAll')"; new = "route('mproducts.all')" },
    @{ old = "action('MproductController@delete',"; new = "route('mproducts.delete'," },
    @{ old = "action('MproductController@add')"; new = "route('mproducts.add')" },
    @{ old = "action('MproductController@edit',"; new = "route('mproducts.edit'," },
    @{ old = "action('BrokerController@delete',"; new = "route('brokers.delete'," },
    @{ old = "action('BrokerController@getOne',"; new = "route('brokers.one'," },
    @{ old = "action('BrokerController@getAll')"; new = "route('brokers.all')" },
    @{ old = "action('BranchController@getOne',"; new = "route('branches.one'," },
    @{ old = "action('BranchController@getAll')"; new = "route('branches.all')" },
    @{ old = "action('BranchController@delete',"; new = "route('branches.delete'," },
    @{ old = "action('StaffController@delete',"; new = "route('staff.delete'," },
    @{ old = "action('StaffController@getOne',"; new = "route('staff.one'," },
    @{ old = "action('StaffController@getAll')"; new = "route('staff.all')" },
    @{ old = "action('StaffController@add')"; new = "route('staff.add')" },
    @{ old = "action('StaffController@edit',"; new = "route('staff.edit'," },
    @{ old = "action('CommissionController@delete',"; new = "route('commission.delete'," },
    @{ old = "action('CommissionController@add')"; new = "route('commission.add')" },
    @{ old = "action('CommissionController@edit')"; new = "route('commission.edit')" },
    @{ old = "action('InboxController@getAll',"; new = "route('inbox.all'," },
    @{ old = "action('ProductController@delete',"; new = "route('products.delete'," },
    @{ old = "action('ProductController@getAll')"; new = "route('products.all')" },
    @{ old = "action('ProductController@add')"; new = "route('products.add')" },
    @{ old = "action('ProductController@edit',"; new = "route('products.edit'," },
    @{ old = "action('ReportController@get',"; new = "route('reports.index'," },
    @{ old = "action('ReportController@get')"; new = "route('reports.index')" },
    @{ old = "action('SettingController@get')"; new = "route('settings.index')" },
    @{ old = "action('SettingController@load')"; new = "route('settings.load')" },
    @{ old = "action('CompanyController@add')"; new = "route('companies.add')" },
    @{ old = "action('CompanyController@editcompanies')"; new = "route('companies.editall')" },
    @{ old = "action('CompanyController@delete',"; new = "route('companies.delete'," }
)

$filesUpdated = 0
$totalReplacements = 0

Write-Host "Processing Blade templates in: $viewsPath"
Write-Host "Found $($bladeFiles.Count) .blade.php files`n"

foreach ($file in $bladeFiles) {
    $content = Get-Content -Path $file.FullName -Raw
    $originalContent = $content
    
    # Apply all mappings
    foreach ($mapping in $mappings) {
        if ($content -like "*$($mapping.old)*") {
            $count = 0
            $content = $content -replace [regex]::Escape($mapping.old), {
                $count++
                $mapping.new
            }
            if ($count -gt 0) {
                $totalReplacements += $count
                Write-Host "  - $($mapping.old) → $($mapping.new) ($count replacements)"
            }
        }
    }
    
    # Write file back if changes were made
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        $filesUpdated++
        Write-Host "✓ Updated: $($file.FullName)"
    }
}

Write-Host "`n========================================`n"
Write-Host "Conversion Complete!"
Write-Host "Files Updated: $filesUpdated"
Write-Host "Total Replacements: $totalReplacements"
Write-Host "========================================`n"
