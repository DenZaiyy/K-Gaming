<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
  private $domPdf;

  public function __construct()
  {
    $this->domPdf = new Dompdf();

    $pdfOptions = new Options();
    $pdfOptions->set("defaultFont", "Arial");
    $this->domPdf->setOptions($pdfOptions);
  }

  public function showPdfFile($html)
  {
    $this->domPdf->loadHtml($html);
    $this->domPdf->render();
    $this->domPdf->stream("facture.pdf", [
      "Attachment" => true,
    ]);
  }

  public function savePdfFile($html, $path)
  {
    $this->domPdf->loadHtml($html);
    $this->domPdf->render();
    $output = $this->domPdf->output();
    file_put_contents($path, $output);
  }
}
