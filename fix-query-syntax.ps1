# Fix All Invalid Query Syntax in Backend Controllers
# Replace ' =>' with standard Eloquent syntax

Write-Host "================================" -ForegroundColor Cyan
Write-Host "FIXING INVALID QUERY SYNTAX" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

$filesFixed = 0
$queriesFixed = 0

# Get all PHP files in Controllers
$controllerFiles = Get-ChildItem -Path "app\Http\Controllers" -Recurse -Filter "*.php"

foreach ($file in $controllerFiles) {
    $content = Get-Content $file.FullName -Raw
    $originalContent = $content
    
    # Pattern 1: where('field', ' =>', $value, 'and') -> where('field', $value)
    $pattern1 = "where\('([^']+)',\s*'\s*=>',\s*([^,]+),\s*'and'\)"
    $replacement1 = "where('`$1', `$2)"
    $content = $content -replace $pattern1, $replacement1
    
    # Pattern 2: where('field', ' =>', $value) -> where('field', $value)  
    $pattern2 = "where\('([^']+)',\s*'\s*=>',\s*([^)]+)\)"
    $replacement2 = "where('`$1', `$2)"
    $content = $content -replace $pattern2, $replacement2
    
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        $filesFixed++
        $matchCount = [regex]::Matches($originalContent, $pattern1).Count + [regex]::Matches($originalContent, $pattern2).Count
        $queriesFixed += $matchCount
        Write-Host "✅ Fixed: $($file.Name) - $matchCount queries" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "================================" -ForegroundColor Cyan
Write-Host "SUMMARY" -ForegroundColor Yellow
Write-Host "Files Fixed: $filesFixed" -ForegroundColor White
Write-Host "Queries Fixed: $queriesFixed" -ForegroundColor White
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "✅ All invalid query syntax has been fixed!" -ForegroundColor Green
Write-Host ""
