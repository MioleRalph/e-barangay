<?php
include('../adminHeader.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file is uploaded successfully
    if (isset($_FILES["excelFile"]) && $_FILES["excelFile"]["error"] == UPLOAD_ERR_OK) {
        // Specify the target directory for file upload
        $targetDir = "../xml/upload/";
        $targetFilePath = $targetDir . basename($_FILES["excelFile"]["name"]);
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowedExtensions = array("xls", "xlsx");
        if (in_array($fileType, $allowedExtensions)) {
            // Move uploaded file to the specified directory
            if (move_uploaded_file($_FILES["excelFile"]["tmp_name"], $targetFilePath)) {
                // Include the required libraries
                require '../xml/excelReader/excel_reader2.php';
                require '../xml/excelReader/SpreadsheetReader.php';

                // Create a SpreadsheetReader object
                $reader = new SpreadsheetReader($targetFilePath);

                // Iterate through each row of the worksheet
                foreach ($reader as $key => $row) {
                    // Check if all required columns exist
                    if (isset($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10])) {
                        // Get cell values for each column
                        $column1 = $row[0];
                        $column2 = $row[1];
                        $column3 = $row[2];
                        $column4 = $row[3];
                        $column5 = $row[4];
                        $column6 = $row[5];
                        $column7 = $row[6];
                        $column8 = $row[7];
                        $column9 = $row[8];
                        $column10 = $row[9];
                        $column11 = $row[10];
                        // Extract other columns similarly

                        // Insert data into the database
                        $sql = "INSERT INTO user (image, fname, sex, age, email, number, username, address, pass, user_type) VALUES ('$column2', '$column3', '$column4', '$column5', '$column6', '$column7', '$column8', '$column9', '$column10', '$column11')";
                        mysqli_query($connForMyDatabase, $sql);
                    } else {
                        // Handle missing columns
                        echo json_encode(array('error' => 'Required columns are missing'));
                        break; // Stop execution
                    }
                }
            } else {
                echo json_encode(array('error' => 'File upload failed'));
            }
        } else {
            echo json_encode(array('error' => 'Only .xls and .xlsx files are allowed'));
        }
    } else {
        echo json_encode(array('error' => 'No file uploaded'));
    }
}
?>


<div class="page-wrapper">
	<div class="content container-fluid">
		<div class="page-header">
			<div class="row align-items-center ">
				<div class="col ">
					<div class="mt-4">
						<h1 class="card-title float-left mt-2 text-center">USER LIST</h1>
					</div>
				</div>
			</div>
		</div>
		
		<div class="card mb-4 mt-3">
			<div class="card-header">
		<form action="" method="post" enctype="multipart/form-data">
			<input type="file" name="excelFile" id="excelFile">
			<button type="submit" class="btn btn-success">Insert</button>
		</form>

			</div>
			<div class="card-body">
				<table id="datatablesSimple">
					<thead>
						<tr>
							<th>#</th>
							<th>Image</th>
							<th>Full Name</th>
							<th>Sex</th>
							<th>Age</th>
							<th>Address</th>
							<th>Contact #</th>
							<th>Email</th>
							<th>Username</th>
							<th>Password</th>
						</tr>
					</thead>

					<?php
					$id = $_SESSION['id'];

					$sql = "SELECT * FROM user WHERE user_type = 'HypeBeast user' ORDER BY id DESC";
					$result = $connforMyOnlineDb->query($sql);

					if ($result->num_rows > 0) {

						$count = 1;

						while ($row = $result->fetch_assoc()) {
							echo "<tr>";
							echo "<td>" . $count . "</td>";
							echo "<td><img src='../images/user-image/" . $row['image'] . "' alt='user Image' class='user-image' style='width: 100px;'></td>";
							echo "<td>" . $row['fname'] . "</td>";
							echo "<td>" . $row['sex'] . "</td>";
							echo "<td>" . $row['age'] . "</td>";
							echo "<td>" . $row['address'] . "</td>";
							echo "<td>" . $row['number'] . "</td>";
							echo "<td>" . $row['email'] . "</td>";
							echo "<td>" . $row['username'] . "</td>";
							echo "<td>" . $row['pass'] . "</td>";
							echo "</tr>";
							$count++;
						}
					} else {
						echo "<tr><td colspan='8' class='text-center text-danger'>No results found</td></tr>"; // Updated colspan to match the number of <th> elements
					}
					$connforMyOnlineDb->close();
					?>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
	function uploadExcel() {
		const fileInput = document.getElementById('excelFile');
		const file = fileInput.files[0];

		// Check if a file is selected
		if (!file) {
			console.error('No file selected');
			return;
		}

		// Read the file
		const reader = new FileReader();
		reader.onload = function(event) {
			const data = new Uint8Array(event.target.result);
			const workbook = XLSX.read(data, {
				type: 'array'
			});

			// Assume the data is in the first sheet
			const sheetName = workbook.SheetNames[0];
			const sheet = workbook.Sheets[sheetName];

			// Parse the sheet data
			const parsedData = XLSX.utils.sheet_to_json(sheet, {
				header: 1
			});

			// Send the parsed data to the server
			fetch('upload.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(parsedData)
				})
				.then(response => {
					if (response.ok) {
						console.log('Data uploaded successfully');
						window.location.reload();
					} else {
						console.error('Data upload failed');
					}
				})
				.catch(error => console.error('Error:', error));
		};
		reader.readAsArrayBuffer(file);
	}
</script>

<script>
	// Add an event listener to the "Insert" button
	document.querySelector('.btn-success').addEventListener('click', function() {
		// Function to upload Excel data
		uploadExcel();
	});

	function uploadExcel() {
		const fileInput = document.getElementById('excelFile');
		const file = fileInput.files[0];

		// Check if a file is selected
		if (!file) {
			console.error('No file selected');
			return;
		}

		// Read the file
		const reader = new FileReader();
		reader.onload = function(event) {
			const data = new Uint8Array(event.target.result);
			const workbook = XLSX.read(data, {
				type: 'array'
			});

			// Assume the data is in the first sheet
			const sheetName = workbook.SheetNames[0];
			const sheet = workbook.Sheets[sheetName];

			// Parse the sheet data
			const parsedData = XLSX.utils.sheet_to_json(sheet, {
				header: 1
			});

			// Send the parsed data to the server using fetch
			fetch('insert.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(parsedData)
				})
				.then(response => {
					if (response.ok) {
						console.log('Data inserted successfully');
						window.location.reload(); // Refresh the page after successful insertion
					} else {
						console.error('Data insertion failed');
					}
				})
				.catch(error => console.error('Error:', error));
		};
		reader.readAsArrayBuffer(file);
	}
</script>

</main>

<?php include('../includes/script.php'); ?>
<?php include('../includes/footer.php'); ?>