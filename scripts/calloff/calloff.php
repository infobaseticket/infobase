<?php
require_once('WkHtmlToPdf.php');


// Create a new WKHtmlToPdf object with some global PDF options
$pdf = new WkHtmlToPdf(array(
    'no-outline',         // Make Chrome not complain
    'margin-top'    => 0,
    'margin-right'  => 0,
    'margin-bottom' => 0,
    'margin-left'   => 0,
));

// Set default page options for all following pages
$pdf->setPageOptions(array(
    'disable-smart-shrinking',
    'user-style-sheet' => 'pdf.css',
));

// Add a HTML file, a HTML string or a page from a URL

//for ($i=0; $i < 10; $i++) { 
    $filesave='new'.$i.'.pdf';
	# code...
	$pdf->addPage('<html><body>hallo Frede</body></html>');
	//$pdf->addPage('http://localhost/mikehaertl-phpwkhtmltopdf/genrate_pdf.php');

	if(!$pdf->saveAs($filesave))
    throw new Exception('Could not create PDF: '.$pdf->getError());
//}



$pdf = file_get_contents($filesave);

header('Content-Type: application/pdf');
header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
header('Pragma: public');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Content-Length: '.strlen($pdf));
header('Content-Disposition: inline; filename="'.basename($filesave).'";');
ob_clean(); 
flush(); 
echo $pdf;
// Add a cover (same sources as above are possible)
//$pdf->addCover('mycover.html');

// Add a Table of contents
//$pdf->addToc();

// Save the PDF


// ... or send to client for inline display
//$pdf->send();

// ... or send to client as file download
//$pdf->send('test.pdf');