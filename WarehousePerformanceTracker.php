<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    $items = [
        ["name" => "Rice 25kg Sack",  "category" => "Grains",        "target" => 50],
        ["name" => "Canned Sardines", "category" => "Canned Goods",  "target" => 80],
        ["name" => "Cooking Oil 1L",  "category" => "Condiments",    "target" => 40],
        ["name" => "Instant Noodles", "category" => "Instant Food",  "target" => 100],
        ["name" => "Bottled Water",   "category" => "Beverages",     "target" => 120],
    ];

    $chosen = 0;
    $sold = [0, 0, 0, 0, 0, 0, 0];
    $analyzed = false;

    if (isset($_POST["analyze"])) {
        $analyzed = true;
        $chosen = (int)$_POST["item"];
        for ($d = 0; $d < 7; $d++) {
            $sold[$d] = (int)$_POST["sold"][$d];
        }
    }

    function analyzer($sold, $target) {
        $total = 0;
        $best = 0;
        $worst = 0;
        for ($d = 0; $d < 7; $d++) {
            $total = $total + $sold[$d];
            if ($sold[$d] > $sold[$best])  { $best = $d; }
            if ($sold[$d] < $sold[$worst]) { $worst = $d; }
        }

        $average = $total / 7;
        $percent = ($total / ($target * 7)) * 100;

        if ($percent >= 90) {
            $status = "HIGH";
        } elseif ($percent >= 60) {
            $status = "MODERATE";
        } else {
            $status = "LOW";
        }

        switch ($status) {
            case "HIGH":
                $remarks = "Good performance. Keep the stock and process as is.";
                break;
            case "MODERATE":
                $remarks = "Needs monitoring. Check the slow days.";
                break;
            case "LOW":
                $remarks = "CRITICAL! Take action now (check stock, staff, and demand).";
                break;
        }

        return [
            "total" => $total, "average" => $average, "percent" => $percent,
            "best" => $best, "worst" => $worst, "status" => $status, "remarks" => $remarks
        ];
    }

    if ($analyzed) {
        $result = analyzer($sold, $items[$chosen]["target"]);
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Warehouse Performance Analyzer</title>
        <style>
            body { font-family: Arial; padding: 20px; max-width: 900px; margin: auto; }
            table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
            th, td { border: 1px solid #999; padding: 8px; text-align: center; }
            th { background: #eee; }
            select { padding: 6px; }
            .box { border: 1px solid #999; padding: 15px; margin-bottom: 20px; border-radius: 6px; }
            .HIGH { color: green; font-weight: bold; }
            .MODERATE { color: orange; font-weight: bold; }
            .LOW { color: red; font-weight: bold; }
            button { padding: 10px 20px; font-size: 15px; cursor: pointer; }
            h1 { color: #2c3e50; }
            h3 { color: #2980b9; }
            .label { color: #555; }
            .value { color: #16a085; font-weight: bold; }
            .best-day { color: green; font-weight: bold; }
            .worst-day { color: red; font-weight: bold; }
            .footer-note { color: #7f8c8d; font-size: 14px; }
        </style>
    </head>
    <body>

    <h1>Warehouse Performance Analyzer</h1>

    <h3>Item Information</h3>
    <table>
        <tr><th>#</th><th>Item</th><th>Category</th><th>Daily Target</th></tr>
        <?php for ($i = 0; $i < 5; $i++) { ?>
            <tr>
                <td><?php echo $i + 1; ?></td>
                <td><?php echo $items[$i]["name"]; ?></td>
                <td><?php echo $items[$i]["category"]; ?></td>
                <td><?php echo $items[$i]["target"]; ?> units</td>
            </tr>
        <?php } ?>
    </table>

    <form method="post" class="box">
        <h3>Select Item</h3>
        <select name="item">
            <?php for ($i = 0; $i < 5; $i++) { ?>
                <option value="<?php echo $i; ?>" <?php if ($i == $chosen) echo "selected"; ?>>
                    <?php echo $items[$i]["name"]; ?>
                </option>
            <?php } ?>
        </select>

        <h3>Units Sold per Day</h3>
        <table>
            <tr>
                <?php for ($d = 1; $d <= 7; $d++) { ?><th>Day <?php echo $d; ?></th><?php } ?>
            </tr>
            <tr>
                <?php for ($d = 0; $d < 7; $d++) { ?>
                    <td>
                        <select name="sold[<?php echo $d; ?>]">
                            <?php for ($n = 0; $n <= 200; $n += 5) { ?>
                                <option value="<?php echo $n; ?>" <?php if ($n == $sold[$d]) echo "selected"; ?>>
                                    <?php echo $n; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </td>
                <?php } ?>
            </tr>
        </table>

        <button type="submit" name="analyze">Analyze</button>
    </form>

    <?php if ($analyzed) { ?>
        <div class="box">
            <h3>Analysis Result: <?php echo $items[$chosen]["name"]; ?></h3>
            <p><span class="label">Daily Target:</span> <span class="value"><?php echo $items[$chosen]["target"]; ?> units</span></p>
            <p><span class="label">Total Sold (7 days):</span> <span class="value"><?php echo $result["total"]; ?></span></p>
            <p><span class="label">Average per Day:</span> <span class="value"><?php echo round($result["average"], 1); ?></span></p>
            <p><span class="label">Performance:</span> <span class="value"><?php echo round($result["percent"]); ?>%</span></p>
            <p>
                <span class="label">Best Day:</span> <span class="best-day">Day <?php echo $result["best"] + 1; ?> (<?php echo $sold[$result["best"]]; ?>)</span>
            | <span class="label">Worst Day:</span> <span class="worst-day">Day <?php echo $result["worst"] + 1; ?> (<?php echo $sold[$result["worst"]]; ?>)</span>
            </p>
            <p><span class="label">Status:</span> <span class="<?php echo $result["status"]; ?>"><?php echo $result["status"]; ?></span></p>
            <p><span class="label">Remarks:</span> <?php echo $result["remarks"]; ?></p>
        </div>
    <?php } ?>

    <p class="footer-note">HIGH = 90% and above | MODERATE = 60% to 89% | LOW = below 60% (CRITICAL)</p>

    
</body>
</html>
