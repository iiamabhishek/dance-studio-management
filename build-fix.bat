@echo off
REM Recreate the Windows junction that vercel-php build needs, then run vercel build
if not exist ".vercel\builders\node_modules\@now\build-utils" (
  if not exist ".vercel\builders\node_modules\@now" mkdir ".vercel\builders\node_modules\@now"
  mklink /J ".vercel\builders\node_modules\@now\build-utils" "..\@vercel\build-utils"
)
vercel build --yes
