<?php
require('fpdf.php');
require('fpdf_merge.php');

class PDF_MC_Table extends FPDF
{
var $widths;
var $aligns;

function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data)
{
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,5,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}
}

 $pdf=new PDF_MC_Table();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',14);
    $pdf->SetWidths(array(75,75));
    $pdf->Row(array("Original Sentence", "Participant Answer"));
    $noQs = 0;


    if ($_POST["page"] == 13) {
      $noQs = 9;
    }
    else {
      $noQs = 11;
    }
    for ($i = 1; $i < $noQs; $i ++) {
        $offset = (($_POST["page"]-1) * 10) + $i;
        $pdf->Row(array(utf8_decode($_POST["label_".$i]), utf8_decode($_POST["trial_".$offset])));
    }

    if ($_POST["page"] == 13) {
        $timeTaken = floor((time() - $_POST["start"])/60);
        $pdf->Row(array("Time taken", $timeTaken." mins"));
    }
    $pdf->Output("".$_POST["participant"].$_POST["page"].".pdf", 'F');

    if ($_POST["page"] > 1) {
        $prevPage = $_POST["page"]-1;
        $merge = new FPDF_Merge();
        $merge->add("".$_POST["participant"].$prevPage.".pdf");
        $merge->add("".$_POST["participant"].$_POST["page"].".pdf");
        $merge->output("".$_POST["participant"].$_POST["page"].".pdf", 'F');

    }

   if ($_POST["page"] == 13) {


    $from        = "Questionnaire ";
    $subject     = $_POST["participant"];
    $mainMessage = "";
    $fileatt     = "".$_POST["participant"].$_POST["page"].".pdf";
    $fileatttype = "application/pdf";
    $fileattname = "".$_POST["participant"].".pdf";
    $headers = "From: $from";

// File
    $file = fopen($fileatt, 'rb');
    $data = fread($file, filesize($fileatt));
    fclose($file);

// This attaches the file
    $semi_rand     = md5(time());
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
    $headers      .= "\nMIME-Version: 1.0\n" .
        "Content-Type: multipart/mixed;\n" .
        " boundary=\"{$mime_boundary}\"";
    $message = "This is a multi-part message in MIME format.\n\n" .
        "-{$mime_boundary}\n" .
        "Content-Type: text/plain; charset=\"iso-8859-1\n" .
        "Content-Transfer-Encoding: 7bit\n\n" .
        $mainMessage  . "\n\n";

    $data = chunk_split(base64_encode($data));
    $message .= "--{$mime_boundary}\n" .
        "Content-Type: {$fileatttype};\n" .
        " name=\"{$fileattname}\"\n" .
        "Content-Disposition: attachment;\n" .
        " filename=\"{$fileattname}\"\n" .
        "Content-Transfer-Encoding: base64\n\n" .
        $data . "\n\n" .
        "-{$mime_boundary}-\n";

// Send the email
    if(mail("2197956w@student.gla.ac.uk", $subject, $message, $headers)) {

        echo "The email was sent.";

    }
    else {

        echo "There was an error sending the mail.";

    }
    }
