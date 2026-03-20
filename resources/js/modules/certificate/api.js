// resources/js/modules/certificate/api.js

export async function fetchUserCertificates(userId) {
    const res = await fetch(`/api/users/${userId}/certificates`);
    return await res.json();
}

export async function fetchCourseCertificates(courseId) {
    const res = await fetch(`/api/courses/${courseId}/certificates`);
    return await res.json();
}

export async function checkDuplicate(userId, courseId) {
    const res = await fetch(
        `/api/certificates/check-duplicate?user_id=${userId}&course_id=${courseId}`
    );
    return await res.json();
}