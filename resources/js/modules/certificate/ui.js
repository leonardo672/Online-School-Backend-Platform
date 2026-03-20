// resources/js/modules/certificate/ui.js

export function updateUserStats(data) {
    document.getElementById('userTotalCertificates').textContent = data.total || 0;
    document.getElementById('userValidCertificates').textContent = data.valid || 0;
    document.getElementById('userExpiredCertificates').textContent = data.expired || 0;

    document.getElementById('userCertificateStats').style.display = 'block';
}

export function updateCourseStats(data) {
    document.getElementById('courseTotalCertificates').textContent = data.total || 0;
    document.getElementById('courseValidCertificates').textContent = data.valid || 0;
    document.getElementById('courseExpiredCertificates').textContent = data.expired || 0;

    document.getElementById('courseCertificateStats').style.display = 'block';
}

export function updatePreview() {
    const userSelect = document.getElementById('user_id');
    const courseSelect = document.getElementById('course_id');
    const certCode = document.getElementById('certificate_code').value;
    const issuedDate = document.getElementById('issued_at').value;

    const userName =
        userSelect.selectedIndex > 0
            ? userSelect.options[userSelect.selectedIndex].dataset.name
            : '[User Name]';

    const courseName =
        courseSelect.selectedIndex > 0
            ? courseSelect.options[courseSelect.selectedIndex].dataset.title
            : '[Course Title]';

    document.getElementById('previewUserName').textContent = userName;
    document.getElementById('previewCourseName').textContent = courseName;
    document.getElementById('previewCertCode').textContent = certCode;

    if (issuedDate) {
        const date = new Date(issuedDate);
        document.getElementById('previewIssuedDate').textContent =
            date.toLocaleDateString();
    }
}