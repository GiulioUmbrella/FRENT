@ECHO OFF

cd C:\xampp\htdocs\css
del /s /q *
cd C:\xampp\htdocs\immagini
del /s /q *
cd C:\xampp\htdocs\php
del /s /q *

cd C:\xampp\htdocs

cd C:\Users\azzal\Progetti\TECHWEB\css
xcopy . "C:\xampp\htdocs\css\" /S /E

cd C:\Users\azzal\Progetti\TECHWEB\php
xcopy . "C:\xampp\htdocs\php\" /S /E

cd C:\Users\azzal\Progetti\TECHWEB\uploads
xcopy . "C:\xampp\htdocs\uploads\" /S /E

cd C:\Users\azzal\Progetti\TECHWEB\immagini
xcopy . "C:\xampp\htdocs\immagini\" /S /E

cd C:\Users\azzal\Progetti\TECHWEB

@ECHO ON