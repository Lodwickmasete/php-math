<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Equation Solver</title>
</head>
<body>
<h2>Equation Solver</h2>
<form id="equationForm">
    <label for="equation">Enter Equation:</label>
    <input type="text" id="equation" name="equation" onkeyup="solveEquation()">
    <br>
    <label for="monomial">Monomial</label>
    <input type="checkbox" id="monomial" name="monomial" value="true">
    <label for="binomial">Binomial</label>
    <input type="checkbox" id="binomial" name="binomial" value="true">
    <label for="trinomial">Trinomial</label>
    <input type="checkbox" id="trinomial" name="trinomial" value="true">
</form>
<div id="result">Waiting for a complete equation...</div>

<script>
function solveEquation() {
    var equation = document.getElementById('equation').value;
    var monomial = document.getElementById('monomial').checked;
    var binomial = document.getElementById('binomial').checked;
    var trinomial = document.getElementById('trinomial').checked;

    if (equation.includes('=')) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status == 200) {
                    document.getElementById('result').innerHTML = xhr.responseText;
                } else {
                    console.log('Error: ' + xhr.status);
                }
            }
        };
        xhr.send('equation=' + encodeURIComponent(equation) + '&monomial=' + monomial + '&binomial=' + binomial + '&trinomial=' + trinomial);
    } else {
        document.getElementById('result').innerHTML = "Waiting for a complete equation...";
    }
}


</script>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['equation'])) {
    $equation = $_POST['equation'];
    $monomial = $_POST['monomial'];
    $binomial = $_POST['binomial'];
    $trinomial = $_POST['trinomial'];

    if ($equation && strpos($equation, '=') !== false) {
        if ($monomial === 'true') {
            $result = solveMonomial($equation);
            echo "<p>Step 1: Monomial Equation Result : x = " . $result . "</p>";
        } elseif ($binomial === 'true') {
            $result = solveBinomial($equation);
            if (is_numeric($result)) {
                echo "<p>Step 1: Binomial Equation Result : x = " . $result . "</p>";
            } else {
                echo "<p>Step 1: Binomial Equation : " . $result . "</p>";
            }
        } elseif ($trinomial === 'true') {
            $result = solveTrinomial($equation);
            if (is_numeric($result)) {
                echo "<p>Step 1: Trinomial Equation Result : x = " . $result . "</p>";
            } else {
                echo "<p>Step 1: Trinomial Equation : x =" . $result . "</p>";
            }
        }
    } else {
        echo "Waiting for a complete equation...";
    }
}

function solveMonomial($equation) {
    // Example: 3x = 9
    // Divide by the coefficient to find the value of x
    // In this example, x = 9/3 = 3
    $parts = explode('=', $equation);
    $right_side = trim($parts[1]);
    $left_side = trim($parts[0]);
    $coeff = (int)$left_side;
    $const = (int)$right_side;
    return $const / $coeff;
}

function solveBinomial($equation) {
    // Example: 2x + 5 = 2
    // Subtract the constant term from both sides to isolate the monomial
    // 2x = 2 - 5 = -3
    // Then call the solveMonomial function to find the value of x
    // In this example, x = -3/2 = -1.5
    $parts = explode('=', $equation);
    $right_side = trim($parts[1]);
    $left_side = trim($parts[0]);
    $terms = explode('+', $left_side);
    $x_terms = array();
    $const = (int)$right_side;
    foreach($terms as $term) {
        if(strpos($term, 'x') !== false) {
            $x_terms[] = (int)$term;
        }
        else {
            $const -= (int)$term;
        }
    }
    $coeff = array_sum($x_terms);
    return solveMonomial($coeff . 'x = ' . $const);
}

function solveTrinomial($equation) {
    // Example: 3x + 4x + 7 = 4
    // Combine like terms and simplify the equation to a binomial or monomial form
    // In this example, 7 - 4 = 3x + 4x
    // Then call the solveBinomial or solveMonomial function to find the value of x
    $parts = explode('=', $equation);
    $right_side = trim($parts[1]);
    $left_side = trim($parts[0]);
    $terms = explode('+', $left_side);
    $x_terms = array();
    $const = (int)$right_side;
    foreach($terms as $term) {
        if(strpos($term, 'x') !== false) {
            $x_terms[] = (int)$term;
        }
        else {
            $const -= (int)$term;
        }
    }
    $coeff = array_sum($x_terms);
    return solveBinomial($coeff . 'x = ' . $const);
}
?>
</body>
</html>
