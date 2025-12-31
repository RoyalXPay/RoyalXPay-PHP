<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">EMS</a></li>
                        <li class="breadcrumb-item active">Videos</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="mb-3">Select Employee</h4>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <select id="employeeSelect" class="form-select">
                            <option value="">Select Employee</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['employee_id'] ?>"
                                    data-code="<?= esc($emp['employee_code']) ?>"
                                    data-designation="<?= esc($emp['designation']) ?>">
                                    <?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?> (<?= esc($emp['employee_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="employeeDetails" class="border p-3 bg-light" style="display:none;">
                    <h5>Employee Information</h5>
                    <table class="table table-bordered">
                        <tr><th>Employee Code</th><td id="empCode"></td></tr>
                        <tr><th>Company Name</th><td id="empCompany"></td></tr>
                        <tr><th>Designation</th><td id="empDesignation"></td></tr>
                        <tr><th>Date & Time</th><td id="empDateTime"></td></tr>
                    </table>

                    <div class="d-flex justify-content-start gap-3">
                        <button class="btn btn-primary" id="frontCameraBtn">Create Front Camera Video</button>
                        <button class="btn btn-secondary" id="backCameraBtn">Create Back Camera Video</button>
                    </div>

                    <div class="mt-4" id="videoSection">
                        <h5>Recorded Video</h5>
                        <video id="recordedVideo" controls style="width:100%; max-width:500px; display:none;"></video>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('employeeSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (!this.value) {
        document.getElementById('employeeDetails').style.display = 'none';
        return;
    }

    document.getElementById('empCode').innerText = selected.dataset.code;
    document.getElementById('empDesignation').innerText = selected.dataset.designation;
    document.getElementById('empCompany').innerText = "<?= session('company_name') ?? 'Company ABC' ?>";
    document.getElementById('empDateTime').innerText = new Date().toLocaleString();
    document.getElementById('employeeDetails').style.display = 'block';
});

async function captureVideo(useFrontCamera = true) {
    const constraints = {
        audio: true,
        video: {
            facingMode: useFrontCamera ? "user" : { exact: "environment" }
        }
    };

    try {
        const stream = await navigator.mediaDevices.getUserMedia(constraints);
        const mediaRecorder = new MediaRecorder(stream);
        let chunks = [];

        mediaRecorder.ondataavailable = e => chunks.push(e.data);
        mediaRecorder.onstop = () => {
            const blob = new Blob(chunks, { type: 'video/mp4' });
            const videoURL = URL.createObjectURL(blob);
            const video = document.getElementById('recordedVideo');
            video.src = videoURL;
            video.style.display = 'block';
        };

        mediaRecorder.start();
        alert("Recording started. Click OK to stop after 5 seconds.");
        setTimeout(() => mediaRecorder.stop(), 5000);
    } catch (err) {
        alert("Error: " + err.message);
    }
}

document.getElementById('frontCameraBtn').addEventListener('click', () => captureVideo(true));
document.getElementById('backCameraBtn').addEventListener('click', () => captureVideo(false));
</script>

<?= $this->endSection() ?>
