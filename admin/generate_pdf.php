<?php
require_once('../vendor/autoload.php'); 

function generatePDF($type, $data) {
    $pdf = new \TCPDF();
    $pdf->SetMargins(0, 0, 0);
    $pdf->AddPage();

    // ---------------- Template Map (form images) ----------------
    $templateMap = [
        'business_permit'         =>'../templates/business_permit.png',
        'business_permit_renewal' =>'../templates/business_permit_renewal.png',
        'indigency'               =>'../templates/indigency.png',
        'barangay_clearance'      =>'../templates/barangay_clearance.png',
        'animal_bite'             =>'../templates/animal_bite.png',
    ];

    if (!isset($templateMap[$type])) {
        throw new Exception("No template found for type: $type");
    }

    // ---------------- Add background image (A4 size 210x297mm) ----------------
    $pdf->Image($templateMap[$type], 0, 0, 210, 297);

    // ---------------- Set font ----------------
    $pdf->SetFont('times', '', 12);

    // ---------------- Fill-in fields ----------------
    if ($type === "business_permit" || $type === "business_permit_renewal") {
        $pdf->SetXY(50, 100); 
        $pdf->Cell(100, 10, $data['name'], 0, 1, 'C');

        $pdf->SetXY(45, 115); 
        $pdf->Cell(100, 10, $data['kind_of_establishment'], 0, 1, 'C');

        $pdf->SetXY(50, 128); 
        $pdf->Cell(100, 10, $data['address'], 0, 1, 'C');

        $pdf->SetXY(50, 145); 
        $pdf->Cell(100, 10, $data['nature_of_business'], 0, 1, 'C');

    }

    if ($type === "indigency") {
        // Compute age from birthdate if provided
        $age = '';
        if (!empty($data['birthday'])) {
            try {
                $birthDate = new DateTime($data['birthday']);
                $today     = new DateTime();
                $age       = $today->diff($birthDate)->y;
            } catch (Exception $e) {
                $age = '';
            }
        }

        // Insert profile picture if provided
        if (!empty($data['picture']) && file_exists($data['picture'])) {
            $pdf->Image(
                $data['picture'],
                155, 77,   // x, y
                23, 23,   // width, height
                '',       // type autodetect
                '', '', false, 300, '', false, false, 0
            );
        }

        // Name
        $pdf->SetXY(77, 103);  
        $pdf->Cell(120, 10, $data['name'], 0, 1);

        // Age (computed)
        $pdf->SetXY(143, 103);
        $pdf->Cell(120, 10, ($age ? $age : "00"), 0, 1);

        // Address
        $pdf->SetXY(50, 113);
        $pdf->MultiCell(120, 10, $data['address'], 0, 'L');
    }

    if ($type === "barangay_clearance") {

        // Compute age from birthdate if provided
        $age = '';
        if (!empty($data['birthday'])) {
            try {
                $birthDate = new DateTime($data['birthday']);
                $today     = new DateTime();
                $age       = $today->diff($birthDate)->y;
            } catch (Exception $e) {
                $age = '';
            }
        }

        // Insert profile picture if provided
        if (!empty($data['picture']) && file_exists($data['picture'])) {
            $pdf->Image(
                $data['picture'],
                149, 78,   // x, y
                23, 26,   // width, height
                '',       // type autodetect
                '', '', false, 300, '', false, false, 0
            );
        }

        $pdf->SetXY(85, 108);
        $pdf->Cell(120, 10, $data['name'], 0, 1);

        $pdf->SetXY(55, 118);
        $pdf->Cell(120, 10, ($age ? $age : "00"), 0, 1);

        $pdf->SetXY(53, 130);
        $pdf->MultiCell(120, 10, $data['address'], 0, 'L');

        $pdf->SetXY(100, 139);
        $pdf->Cell(100, 10,  $data['years_stay_in_barangay'], 0, 1, 'L');

        $pdf->SetXY(42, 167);
        $pdf->Cell(100, 10,  $data['purpose'], 0, 1, 'L');

    }

    if ($type === "animal_bite") {
        $pdf->SetFont('dejavusans', '', 10); // make sure checkmark is supported

        // Full Name
        $pdf->SetXY(55, 80);
        $pdf->Cell(120, 10, $data['full_nameA'], 0, 1);

        // Guardian
        $pdf->SetXY(100, 95);
        $pdf->Cell(120, 10, $data['guardian'], 0, 1);

        // Address
        $pdf->SetXY(55, 113);
        $pdf->MultiCell(120, 10, $data['address'], 0, 'L');

        // Age
        $pdf->SetXY(50, 118);
        $pdf->Cell(120, 10, $data['age'], 0, 1);

        // DOB
        $pdf->SetXY(80, 118);
        $pdf->Cell(120, 10, $data['dob'], 0, 1);



        // =============================
        // GENDER (Male / Female checkbox)
        // =============================
        if ($data['gender'] === "Male") {
            $pdf->SetXY(120, 118); // adjust to your "Male" checkbox
            $pdf->Cell(10, 10, '✓', 0, 0, 'C');
        } elseif ($data['gender'] === "Female") {
            $pdf->SetXY(135, 118); // adjust to your "Female" checkbox
            $pdf->Cell(10, 10, '✓', 0, 0, 'C');
        }

        // Contact
        $pdf->SetXY(60, 130);
        $pdf->Cell(120, 10, $data['contact'], 0, 1);

        // Bite Location
        $pdf->SetXY(60, 150);
        $pdf->MultiCell(120, 10, $data['bite_location'], 0, 'L');

        // Body Part
        $pdf->SetXY(60, 160);
        $pdf->MultiCell(120, 10, $data['body_part'], 0, 'L');

        // Washed
        $pdf->SetXY(60, 170);
        $pdf->Cell(120, 10, $data['washed'], 0, 1);

        // Bite Date
        $pdf->SetXY(60, 180);
        $pdf->Cell(120, 10, $data['bite_date'], 0, 1);

        // Animal Description
        $pdf->SetXY(60, 190);
        $pdf->MultiCell(120, 10, $data['animal_description'], 0, 'L');

        // Color
        $pdf->SetXY(60, 200);
        $pdf->Cell(120, 10, $data['color'], 0, 1);

        // Marks
        $pdf->SetXY(60, 210);
        $pdf->MultiCell(120, 10, $data['marks'], 0, 'L');

        // =============================
        // Animal Condition (Nakakulong / Nakatali / Gala)
        // =============================
        if (!empty($data['animal_condition'])) {
            foreach ((array)$data['animal_condition'] as $cond) {
                switch ($cond) {
                    case "Nakakulong":
                        $pdf->SetXY(90, 220); // coords for Nakakulong
                        $pdf->Cell(10, 10, '✓', 0, 0, 'C');
                        break;
                    case "Nakatali":
                        $pdf->SetXY(110, 220); // coords for Nakatali
                        $pdf->Cell(10, 10, '✓', 0, 0, 'C');
                        break;
                    case "Gala":
                        $pdf->SetXY(130, 220); // coords for Gala
                        $pdf->Cell(10, 10, '✓', 0, 0, 'C');
                        break;
                }
            }
        }

        // =============================
        // Registered (Oo / Hindi)
        // =============================
        if ($data['registered'] === "Oo") {
            $pdf->SetXY(90, 230); // coords for Oo
            $pdf->Cell(10, 10, '✓', 0, 0, 'C');
        } else {
            $pdf->SetXY(110, 230); // coords for Hindi
            $pdf->Cell(10, 10, '✓', 0, 0, 'C');
        }

        // =============================
        // Other Animals (Meron / Wala)
        // =============================
        if ($data['other_animals'] === "Meron") {
            $pdf->SetXY(90, 240); // coords for Meron
            $pdf->Cell(10, 10, '✓', 0, 0, 'C');
        } else {
            $pdf->SetXY(110, 240); // coords for Wala
            $pdf->Cell(10, 10, '✓', 0, 0, 'C');
        }

        // =============================
        // Dog Condition (Malusog / Bagong panganak / May sakit)
        // =============================
        switch ($data['dog_condition']) {
            case "Malusog":
                $pdf->SetXY(90, 250);
                $pdf->Cell(10, 10, '✓', 0, 0, 'C');
                break;
            case "Bagong panganak":
                $pdf->SetXY(110, 250);
                $pdf->Cell(10, 10, '✓', 0, 0, 'C');
                break;
            case "May sakit":
                $pdf->SetXY(130, 250);
                $pdf->Cell(10, 10, '✓', 0, 0, 'C');
                break;
        }

        // Owner Name
        $pdf->SetXY(60, 260);
        $pdf->Cell(120, 10, $data['owner_name'], 0, 1);

        // Date Today
        $pdf->SetXY(40, 280);
        $pdf->Cell(100, 10, date('jS \of F Y'), 0, 1, 'L');
    }

    // ---------------- Save file ----------------
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

    return $relativePath;
}
