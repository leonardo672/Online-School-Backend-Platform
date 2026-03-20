// resources/js/modules/certificate/validation.js

export function validateForm() {
    const userId = document.getElementById('user_id').value;
    const courseId = document.getElementById('course_id').value;
    const code = document.getElementById('certificate_code').value;

    if (!userId) return 'Please select a user';
    if (!courseId) return 'Please select a course';
    if (!code) return 'Certificate code is required';

    return null;
}