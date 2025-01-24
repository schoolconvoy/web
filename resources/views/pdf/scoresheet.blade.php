<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>FIRST TERM STUDENT'S PERFORMANCE REPORT</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10px;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .page-container {
            padding: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        .school-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .school-header h1 {
            margin: 2px 0;
            font-size: 16px;
        }
        .school-header p {
            margin: 0;
            font-size: 11px;
        }
        .report-title {
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            margin: 5px 0;
            font-size: 13px;
        }
        .top-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .top-info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .top-info-label {
            font-weight: bold;
        }

        /* Container for Grade Legend & Summary Box side-by-side */
        .grade-summary-container {
            display: flex;
            align-items: flex-start;
            gap: 10px;         /* spacing between the two tables */
            margin-bottom: 8px;
        }
        .grade-legend, .summary-box {
            border: 1px solid #000;
            border-collapse: collapse;
        }
        .grade-legend {
            width: 30%;       /* 2/3 of the container */
        }
        .summary-box {
            width: 33%;       /* 1/3 of the container (50% smaller than legend) */
        }

        .grade-legend th, .grade-legend td,
        .summary-box th, .summary-box td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
            font-size: 11px;
        }

        .grade-legend th, .summary-box th, .color-coded {
            background-color: #9BC2E6;
        }

        .section-heading {
            font-weight: bold;
            text-decoration: underline;
            margin: 8px 0 3px 0;
            font-size: 12px;
        }
        .subjects-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .subjects-table th, .subjects-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
            font-size: 11px;
        }
        .subjects-table th {
            background-color: #9BC2E6;
            font-weight: 600;
        }
        .domains-container {
            width: 100%;
            display: table;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .domain {
            display: table-cell;
            vertical-align: top;
            padding: 2px;
        }
        .domain table {
            width: 100%;
            border-collapse: collapse;
        }
        .domain table th, .domain table td {
            border: 1px solid #000;
            padding: 3px 4px;
            font-size: 11px;
            text-align: center;
        }
        .domain table th {
            background-color: #9BC2E6;
        }
        .domain table td:first-child {
            text-align: left;
        }
        .remarks-section {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .remarks-section td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 11px;
        }
        .footer-info {
            width: 100%;
            margin-top: 8px;
            border-collapse: collapse;
            font-size: 11px;
        }
        .footer-info td {
            padding: 3px;
            vertical-align: top;
        }
        .signature-line {
            margin-top: 10px;
            width: 90px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 10px;
        }
        .right {
            text-align: right;
        }
        .rotate-text {
            /* writing-mode: vertical-rl;
            transform: rotate(180deg); */
        }
    </style>
</head>
<body>
<div class="page-container">
    <!-- School Header -->
    <div class="school-header-container">
        <img src="/android-chrome-512x512.png" style="position: absolute; top: 0; left: 100px; height: 100px; width: 100px;" />
        <div class="school-header">
            <h1>INTERGUIDE HIGH SCHOOL</h1>
            <strong>6/8 Toyin Street, Ikeja, Lagos State.<br/>
                Tel: 07038950017, 08145565893 Email: info@interguideschools.com</strong>
        </div>
    </div>
    <div class="report-title">
        {{ $data['term'] }} STUDENT'S PERFORMANCE REPORT
    </div>

    <!-- Student Info -->
    <table class="top-info-table">
        <tr>
            <td><span class="top-info-label">NAME:</span> {{ $data['student']['name'] }}</td>
            <td><span class="top-info-label">SESSION:</span> {{ $data['session'] }}</td>
            <td><span class="top-info-label">GENDER:</span> {{ $data['student']['gender'] }}</td>
        </tr>
        <tr>
            <td><span class="top-info-label">CLASS:</span> {{ $data['student']['class'] }}</td>
            <td><span class="top-info-label">ADMISSION NO:</span> {{ $data['student']['admission_no'] }}</td>
            <td><span class="top-info-label">D.O.B.:</span> {{ $data['student']['dob'] }} &nbsp;&nbsp; <span class="top-info-label">AGE:</span> 10 Years</td>
        </tr>
        <tr>
            <td><span class="top-info-label">ATTENDANCE:</span> 116</td>
            <td><span class="top-info-label">TOTAL SUBJECT OFFERED:</span> 18</td>
            <td></td>
        </tr>
    </table>

    <!-- Container holding both tables side by side -->
    <div class="domains-container">
        <div class="domain" style="width: 30%">
            <!-- Grade Legend -->
            <table class="grade-legend">
                <tr>
                    <th>GRADE</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>E</th>
                    <th>F</th>
                </tr>
                <tr>
                    <td class="color-coded">NO</td>
                    <td>11</td>
                    <td>7</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align:center;font-weight:bold;" class="color-coded">
                        TOTAL SUBJECT OFFERED
                    </td>
                    <td>18</td>
                </tr>
            </table>
        </div>

        <div class="domain" style="width: 30%">
            <!-- Summary Box -->
            <table class="summary-box">
                <tr>
                    <th>PERCENTAGE</th>
                    <td>79.4</td>
                </tr>
                <tr>
                    <th>GRADE</th>
                    <td>B+</td>
                </tr>
                <tr colspan="2">
                    <td>VERY GOOD</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Cognitive Domain -->
    <div class="section-heading">COGNITIVE DOMAIN</div>
    <table class="subjects-table">
        <thead>
        <tr>
            <th rowspan="2" style="vertical-align: middle;">SUBJECTS</th>
            <th colspan="2">1ST TERM</th>
            <th rowspan="2" style="vertical-align: middle;"><span class="rotate-text">TOTAL<br>(100)</span></th>
            <th rowspan="2" style="vertical-align: middle;"><span class="rotate-text">2ND TERM<br>(100)</span></th>
            <th rowspan="2" style="vertical-align: middle;"><span class="rotate-text">3RD TERM<br>(100)</span></th>
            <th rowspan="2" style="vertical-align: middle;"><span class="rotate-text">CUMM<br>(300)</span></th>
            <th rowspan="2" style="vertical-align: middle;"><span class="rotate-text">PERCENT.<br>(%)</span></th>
            <th rowspan="2" style="vertical-align: middle;"><span class="rotate-text">GRADE</span></th>
            <th rowspan="2" style="vertical-align: middle;"><span class="rotate-text">CLASS<br>POS.</span></th>
            <th rowspan="2" style="vertical-align: middle;"><span class="rotate-text">CLASS<br>MIN</span></th>
            <th rowspan="2" style="vertical-align: middle;"><span class="rotate-text">CLASS<br>MAX</span></th>
            <th rowspan="2" style="vertical-align: middle;"><span class="rotate-text">CLASS<br>AVG</span></th>
            <th rowspan="2" style="vertical-align: middle;"><span class="rotate-text">REMARKS</span></th>
        </tr>
        <tr>
            <th>CA<br>(40)</th>
            <th>EXAM<br>(60)</th>
        </tr>
        </thead>
        <tbody>
        <!-- Example row; repeat for all subjects as needed -->
        <tr>
            <td>Mathematics</td>
            <td>35</td>
            <td>30</td>
            <td>65</td>
            <td>81</td>
            <td>83</td>
            <td>229</td>
            <td>65.0</td>
            <td>B</td>
            <td>8th</td>
            <td>32</td>
            <td>90</td>
            <td>61.1</td>
            <td>GOOD</td>
        </tr>
        <tr>
            <td>English Studies</td>
            <td>36</td>
            <td>55</td>
            <td>91</td>
            <td>81</td>
            <td>85</td>
            <td>257</td>
            <td>85.7</td>
            <td>A</td>
            <td>3rd</td>
            <td>23</td>
            <td>91</td>
            <td>59.0</td>
            <td>EXCELLENT</td>
        </tr>
        <!-- Etc... -->
        </tbody>
    </table>

    <p style="font-size: 11px;">
        <strong>Grading:</strong> 90 - 100% = A+ (Distinction), 80 - 89.9% = A (Excellent),
        70 - 79.9% = B (Very Good), 60 - 69.9% = B (Good), 50 - 59.9% = C (Average),
        40 - 49.9% = D (Fair), 10 - 39.9% = F (Weak).
    </p>

    <!-- Affective and Psychomotor Domains -->
    <div class="domains-container">
        <div class="domain" style="width: 34%;">
            <table>
                <tr><th colspan="2">AFFECTIVE DOMAIN</th></tr>
                <tr><td>Attentiveness</td><td>5</td></tr>
                <tr><td>Honesty</td><td>4</td></tr>
                <!-- etc. -->
            </table>
        </div>
        <div class="domain" style="width: 32%;">
            <table>
                <tr><th>RATING INDICES</th></tr>
                <tr><td>5 = Excellent degree of traits</td></tr>
                <!-- etc. -->
            </table>
        </div>
        <div class="domain" style="width: 34%;">
            <table>
                <tr><th colspan="2">PSYCHOMOTOR DOMAIN</th></tr>
                <tr><td>Handling Tools</td><td>4</td></tr>
                <!-- etc. -->
            </table>
        </div>
    </div>

    <!-- Remarks -->
    <table class="remarks-section">
        <tr>
            <td>
                <strong>Gabriella is kind, respectful, and always cheerful.</strong>
                She completes tasks when due and takes responsibility in handling her belongings.
            </td>
        </tr>
    </table>

    <!-- Footer Info -->
    <table class="footer-info">
        <tr>
            <td>
                <strong>Teacher's Name:</strong> Mrs. Adejoh Patience <br>
                <strong>Teacher's Remark:</strong> An impressive result from an amazing student, keep it up.
                <div class="signature-line">Signature</div>
            </td>
            <td class="right">
                <strong>Principal's Name:</strong> Mr. Audu B. Olayinka <br>
                <strong>Principal's Remark:</strong>
                <div class="signature-line">Signature</div>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Next Term Begins:</strong> Monday, 6th January, 2025.
            </td>
            <td class="right">
                <strong>Date:</strong> 17/12/2024
            </td>
        </tr>
    </table>
</div>
</body>
</html>
