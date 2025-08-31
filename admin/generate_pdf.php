<?php
require_once('../vendor/autoload.php'); 
// ❌ remove: use TCPDF; (not namespaced)

function generatePDF($type, $data) {
    $pdf = new \TCPDF();
    $pdf->SetMargins(20, 20, 20);
    $pdf->AddPage();
    $pdf->SetFont('times', '', 12);

    $html = '';

    // ---------------- BUSINESS PERMIT ----------------
    if ($type === "business_permit") {
        $html = '
        <div style="text-align:center;">
            <img src="../assets/logo-left.png" height="60" style="float:left;">
            <img src="../assets/logo-right.png" height="60" style="float:right;">
            <h4>REPUBLIKA NG PILIPINAS<br>LALAWIGAN NG LAGUNA<br>LUNGSOD NG CALAMBA<br>BARANGAY SAMPIRUHAN</h4>
            <h3 style="color:#000080;">Barangay Business Clearance</h3>
        </div>
        <br><br>
        <p>To whom it may concern:</p>
        <p style="text-align:justify;">
            Pursuant to the provisions of Barangay Tax Ordinance No.1, series of 1995 as amended, 
            this CERTIFICATE is hereby granted to:
        </p>
        <h2 style="text-align:center;">'.htmlspecialchars($data['name']).'</h2>
        <p style="text-align:center;"><i>(Name of Applicant)</i></p>
        <h3 style="text-align:center;">'.htmlspecialchars($data['kind_of_establishment']).'</h3>
        <p style="text-align:center;"><i>(Name/Kind of Establishment)</i></p>
        <p style="text-align:center;">Resident of '.htmlspecialchars($data['address']).' to operate and maintain</p>
        <h3 style="text-align:center;">'.htmlspecialchars($data['nature_of_business']).'</h3>
        <p style="text-align:center;"><i>(Nature of Business)</i></p>
        <p style="text-align:justify;">
            at Barangay Sampiruhan, Calamba City, subject however to the provisions of existing 
            laws, ordinances, rules, and regulations governing the operation and maintenance of the same.
        </p>
        <br><br>
        <p>Date Issued: '.date('F d, Y').'</p>
        <br><br><br>
        <p style="text-align:right;">
            <b>Iggs. James Philip C. Dumalaon</b><br>
            <i>Punong Barangay</i>
        </p>';
    }

    // ---------------- INDIGENCY ----------------
    if ($type === "indigency") {
        $html = '
        <div style="text-align:center;">
            <img src="../assets/logo-left.png" height="60" style="float:left;">
            <img src="../assets/logo-right.png" height="60" style="float:right;">
            <h4>REPUBLIKA NG PILIPINAS<br>LALAWIGAN NG LAGUNA<br>LUNGSOD NG CALAMBA<br>BARANGAY SAMPIRUHAN</h4>
            <h3 style="color:#000080;">CERTIFICATE OF INDIGENCY</h3>
        </div>
        <br><br>
        <p>Sa Kinauukulan,</p>
        <p style="text-align:justify;">
            Ito ay nagpapatunay na si <b>'.htmlspecialchars($data['name']).'</b>, '.$data['age'].' taong gulang, 
            naninirahan sa '.htmlspecialchars($data['address']).', Barangay Sampiruhan, Lungsod ng Calamba, Lalawigan ng Laguna 
            ay walang sapat na pinagkakakitaan at nabibilang sa mahihirap na pamilya sa aming Barangay.
        </p>
        <p style="text-align:justify;">
            Ang pagpapatunay na ito ay ipinagkaloob ng Sangguniang Barangay ng Sampiruhan ayon sa kanyang kahilingan upang magsilbing 
            katibayan at magamit sa anumang pangangailangan at kapakinabangan legal.
        </p>
        <br><br>
        <table width="100%">
            <tr>
                <td width="50%">Lagda ng humihiling:<br><br>__________________________</td>
                <td width="50%" align="right">Pinagtibay ni:<br><br><b>Iggs. James Philip C. Dumalaon</b><br><i>Punong Barangay</i></td>
            </tr>
        </table>
        <br><br>
        <small>
            CTC No.: ____________________<br>
            Date issued: '.date('F d, Y').'<br>
            Valid for : 6 months<br>
            Issued by : _______________<br>
            <i>*Not Valid w/o Dry Seal</i>
        </small>';
    }

    // ---------------- BARANGAY CLEARANCE ----------------
    if ($type === "barangay_clearance") {
        $html = '
        <div style="text-align:center;">
            <img src="../assets/logo-left.png" height="60" style="float:left;">
            <img src="../assets/logo-right.png" height="60" style="float:right;">
            <h4>REPUBLIKA NG PILIPINAS<br>LALAWIGAN NG LAGUNA<br>LUNGSOD NG CALAMBA<br>BARANGAY SAMPIRUHAN</h4>
            <h3 style="color:#000080;">BARANGAY CLEARANCE</h3>
        </div>
        <br><br>
        <p>To whom it may concern:</p>
        <p style="text-align:justify;">
            This is to certify that <b>'.htmlspecialchars($data['name']).'</b>, of legal age, residing at '.htmlspecialchars($data['address']).',
            is a resident of Barangay Sampiruhan, Calamba City.
        </p>
        <p style="text-align:justify;">
            This certification is issued upon request of the above-named person for whatever legal purpose it may serve him/her best.
        </p>
        <br><br>
        <p>Issued this '.date('jS').' day of '.date('F Y').' at Barangay Sampiruhan, Calamba City.</p>
        <br><br><br>
        <p style="text-align:right;">
            <b>Iggs. James Philip C. Dumalaon</b><br>
            <i>Punong Barangay</i>
        </p>';
    }

    // ---------------- ANIMAL BITE REPORT ----------------
    if ($type === "animal_bite") {
        $html = '
        <div style="text-align:center;">
            <img src="../assets/logo-left.png" height="60" style="float:left;">
            <img src="../assets/logo-right.png" height="60" style="float:right;">
            <h4>REPUBLIKA NG PILIPINAS<br>LALAWIGAN NG LAGUNA<br>LUNGSOD NG CALAMBA<br>BARANGAY SAMPIRUHAN</h4>
            <h3 style="color:#000080;">ANIMAL BITE REPORT</h3>
        </div>
        <br><br>
        <p>This is to certify that a report has been filed regarding an <b>Animal Bite Incident</b>.</p>
        <p><b>Victim:</b> '.htmlspecialchars($data['name']).'</p>
        <p><b>Address:</b> '.htmlspecialchars($data['address']).'</p>
        <p><b>Details:</b> '.$data['incident_details'].'</p>
        <br><br>
        <p>Issued this '.date('jS').' day of '.date('F Y').' at Barangay Sampiruhan, Calamba City.</p>
        <br><br><br>
        <p style="text-align:right;">
            <b>Iggs. James Philip C. Dumalaon</b><br>
            <i>Punong Barangay</i>
        </p>';
    }

    // ---------- Write and Save ----------
    $pdf->writeHTML($html, true, false, true, false, '');

    // ✅ Build absolute + relative paths
    $basePath = __DIR__ . "/../uploads/";
    $userFolder = $basePath . preg_replace('/[^A-Za-z0-9_\-]/', '_', $data['name']);
    $reqFolder  = $userFolder . "/" . $type;

    if (!is_dir($reqFolder)) {
        mkdir($reqFolder, 0777, true);
    }

    $absolutePath = $reqFolder . "/" . $type . "_" . $data['id'] . ".pdf";
    $relativePath = "uploads/" . preg_replace('/[^A-Za-z0-9_\-]/', '_', $data['name']) 
                  . "/" . $type . "/" . $type . "_" . $data['id'] . ".pdf";

    $pdf->Output($absolutePath, 'F');

    return $relativePath; // ✅ return relative path for DB
}
