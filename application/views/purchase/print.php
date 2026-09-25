<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase - <?= htmlspecialchars($header->purchase_code) ?></title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/bootstrap/css/bootstrap.min.css') ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #000;
            background: #f4f6f9;
        }

        .print-container {
            max-width: 1000px;
            margin: 30px auto;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .document-title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .document-code {
            font-size: 18px;
            color: #555;
        }

        .table th {
            vertical-align: middle !important;
        }

        .table td {
            vertical-align: middle !important;
        }

        .info-table td {
            padding: 3px 0;
            border: none !important;
        }

        .total-table td {
            padding: 5px 8px;
        }

        .signature {
            margin-top: 70px;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-space {
            height: 70px;
        }

        .no-print {
            margin-bottom: 20px;
        }

        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }

            body {
                font-size: 16px;
                background: #fff !important;
            }

            .print-container {
                max-width: none;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            .table-bordered {
                border: 1px solid #000 !important;
            }

            .table-bordered th,
            .table-bordered td {
                border: 1px solid #000 !important;
            }

            .badge {
                border: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body class="hold-transition">

<div class="wrapper">

    <div class="container print-container">

        <!-- BUTTON -->
        <div class="no-print text-right">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>

            <button type="button" class="btn btn-secondary" onclick="window.close()">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>

        <!-- PURCHASE -->
        <div class="card">

            <div class="card-body">

                <!-- HEADER -->
                <div class="row mb-4">

                    <div class="col-7">

                        <div class="company-name">
                            Nama Perusahaan
                        </div>

                        <div>
                            Alamat perusahaan
                        </div>

                        <div>
                            Telp: 08xxxxxxxxxx
                        </div>

                        <div>
                            Email: email@perusahaan.com
                        </div>

                    </div>

                    <div class="col-5 text-right">

                        <div class="document-title">
                            PURCHASE
                        </div>

                        <div class="document-code">
                            <?= htmlspecialchars($header->purchase_code) ?>
                        </div>

                    </div>

                </div>

                <hr>

                <!-- INFORMATION -->
                <div class="row mb-4">

                    <div class="col-6">

                        <table class="table table-sm info-table">

                            <tr>
                                <td width="35%">Supplier</td>
                                <td width="5%">:</td>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($header->nama_supplier) ?>
                                    </strong>
                                </td>
                            </tr>

                            <tr>
                                <td>Tanggal Purchase</td>
                                <td>:</td>
                                <td>
                                    <?= htmlspecialchars($header->purchase_date) ?>
                                </td>
                            </tr>

                            <tr>
                                <td>Jatuh Tempo</td>
                                <td>:</td>
                                <td>
                                    <?= !empty($header->due_date)
                                        ? htmlspecialchars($header->due_date)
                                        : '-' ?>
                                </td>
                            </tr>

                        </table>

                    </div>

                    <div class="col-6">

                        <table class="table table-sm info-table">

                            <tr>
                                <td width="35%">Pembayaran</td>
                                <td width="5%">:</td>
                                <td>
                                    <?= htmlspecialchars($header->payment_type) ?>
                                </td>
                            </tr>

                            <tr>
                                <td>Status</td>
                                <td>:</td>
                                <td>
                                    <?= htmlspecialchars($header->product_status_name) ?>
                                </td>
                            </tr>

                            <tr>
                                <td>Dibuat Oleh</td>
                                <td>:</td>
                                <td>
                                    <?= !empty($header->created_by_username)
                                        ? htmlspecialchars($header->created_by_username)
                                        : '-' ?>
                                </td>
                            </tr>

                        </table>

                    </div>

                </div>

                <!-- DETAIL -->
                <table class="table table-bordered table-sm">

                    <thead>
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Produk</th>
                            <th width="12%">Qty</th>
                            <th width="18%">Harga</th>
                            <th width="20%">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $no = 1;
                        $total = 0;
                        ?>

                        <?php foreach ($details as $item): ?>

                            <?php
                            $subtotal = (float) $item->qty * (float) $item->price;
                            $total += $subtotal;
                            ?>

                            <tr>

                                <td class="text-center">
                                    <?= $no++ ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item->product_name) ?>
                                </td>

                                <td class="text-center">
                                    <?= htmlspecialchars($item->qty) ?>
                                </td>

                                <td class="text-right">
                                    <?= number_format($item->price, 0, ',', '.') ?>
                                </td>

                                <td class="text-right">
                                    <?= number_format($subtotal, 0, ',', '.') ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

                <!-- TOTAL -->
                <div class="row justify-content-end mt-3 ">

                    <div class="col-md-5">

                        <table class="table table-bordered table-sm total-table">

                            <tr>
                                <td>
                                    Subtotal
                                </td>

                                <td width="45%" class="text-right">
                                    <?= number_format($total, 0, ',', '.') ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Diskon
                                </td>

                                <td class="text-right">
                                    <?= number_format($header->discount, 0, ',', '.') ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Pajak
                                </td>

                                <td class="text-right">
                                    <?= number_format($header->tax, 0, ',', '.') ?>
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    Grand Total
                                </th>

                                <th class="text-right">
                                    <?= number_format($header->grand_total, 0, ',', '.') ?>
                                </th>
                            </tr>

                        </table>

                    </div>

                </div>

                <!-- NOTES -->
                <?php if (!empty($header->notes)): ?>

                    <div class="mt-4">

                        <strong>
                            Catatan:
                        </strong>

                        <div class="mt-1">
                            <?= nl2br(htmlspecialchars($header->notes)) ?>
                        </div>

                    </div>

                <?php endif; ?>

                <!-- SIGNATURE -->
                <div class="row signature">

                    <div class="col-6 text-center">

                        <div class="signature-box mx-auto">

                            Dibuat Oleh

                            <div class="signature-space"></div>

                            <strong>
                                <?= !empty($header->created_by_username)
                                    ? htmlspecialchars($header->created_by_username)
                                    : '________________' ?>
                            </strong>

                        </div>

                    </div>

                    <div class="col-6 text-center">

                        <div class="signature-box mx-auto">

                            Disetujui Oleh

                            <div class="signature-space"></div>

                            <strong>
                                ____________________
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    window.onload = function () {
        window.print();
    };
</script>

</body>
</html>