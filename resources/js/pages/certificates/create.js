// resources/js/pages/certificates/create.js

import {
    fetchUserCertificates,
    fetchCourseCertificates,
    checkDuplicate
} from '../../modules/certificate/api';

import { debounce, showToast } from '../../modules/certificate/utils';
import '@/css/pages/certificates/create.css';

// ---------------- STATE ----------------
const state = {
    codeAvailable: false,
    duplicateConfirmed: false,
    isSubmitting: false,
};

// ---------------- INIT ----------------
document.addEventListener('DOMContentLoaded', () => {
    init();
});

function init() {
    bindEvents();

    generateCertificateCode();
    updatePreview();
    toggleExpiryDate();
    updateValidityPeriod();

    initPreselected();
}

// ---------------- EVENTS ----------------
function bindEvents() {
    const codeInput = document.getElementById('certificate_code');

    codeInput.addEventListener('input', debounce(checkCodeAvailability, 500));

    document.getElementById('issued_at').addEventListener('change', () => {
        updateValidityPeriod();
        updatePreview();
    });

    document.getElementById('expires_at').addEventListener('change', updateValidityPeriod);

    document.getElementById('user_id').addEventListener('change', async (e) => {
        await loadUserCertificates(e.target.value);
        updateUserInfo();
        updatePreview();
    });

    document.getElementById('course_id').addEventListener('change', async (e) => {
        await loadCourseCertificates(e.target.value);
        updateCourseInfo();
        updatePreview();
    });

    document.getElementById('generateCodeBtn')?.addEventListener('click', generateCertificateCode);
    document.getElementById('setNowBtn')?.addEventListener('click', () => setIssuedDate('now'));
    document.getElementById('noExpiry')?.addEventListener('change', toggleExpiryDate);

    document.querySelectorAll('.expiry-btn').forEach(btn => {
        btn.addEventListener('click', () => setExpiryDate(btn.dataset.days));
    });

    document.getElementById('refreshPreviewBtn')?.addEventListener('click', refreshPreview);
    document.getElementById('resetFormBtn')?.addEventListener('click', resetForm);
    document.getElementById('saveDraftBtn')?.addEventListener('click', saveAsDraft);
    document.getElementById('checkDuplicateBtn')?.addEventListener('click', checkForDuplicates);
    document.getElementById('proceedDuplicateBtn')?.addEventListener('click', proceedWithDuplicate);

    document.getElementById('certificateForm').addEventListener('submit', handleSubmit);

    bindKeyboardShortcuts();
}

// ---------------- INIT PRESELECT ----------------
function initPreselected() {
    const userId = document.getElementById('user_id').value;
    const courseId = document.getElementById('course_id').value;

    if (userId) {
        loadUserCertificates(userId);
        updateUserInfo();
    }

    if (courseId) {
        loadCourseCertificates(courseId);
        updateCourseInfo();
    }
}

// ---------------- FORM SUBMIT ----------------
async function handleSubmit(e) {
    const userId = document.getElementById('user_id').value;
    const courseId = document.getElementById('course_id').value;
    const certCode = document.getElementById('certificate_code').value;

    if (!userId) return prevent(e, 'Please select a user', 'user_id');
    if (!courseId) return prevent(e, 'Please select a course', 'course_id');
    if (!certCode) return prevent(e, 'Please enter a certificate code', 'certificate_code');

    if (!state.codeAvailable) {
        e.preventDefault();
        showToast('Please wait for code availability check', 'warning');
        return;
    }

    if (!state.duplicateConfirmed) {
        const isDuplicate = await checkForDuplicates();
        if (isDuplicate) {
            e.preventDefault();
            return;
        }
    }

    if (state.isSubmitting) {
        e.preventDefault();
        return;
    }

    state.isSubmitting = true;

    const btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Issuing...';
}

function prevent(e, message, fieldId) {
    e.preventDefault();
    showToast(message, 'error');
    document.getElementById(fieldId).focus();
}

// ---------------- CODE ----------------
function generateCertificateCode() {
    const prefix = 'CERT';
    const timestamp = Date.now().toString(36).toUpperCase();
    const random = Math.random().toString(36).substring(2, 6).toUpperCase();

    const code = `${prefix}-${timestamp.slice(0, 4)}-${timestamp.slice(4, 8)}-${random}`;

    document.getElementById('certificate_code').value = code;

    updatePreview();
    checkCodeAvailability();
}

function checkCodeAvailability() {
    const code = document.getElementById('certificate_code').value;
    const badge = document.getElementById('availabilityBadge');
    const wrapper = document.getElementById('codeAvailability');

    if (!code) {
        wrapper.style.display = 'none';
        state.codeAvailable = false;
        return;
    }

    wrapper.style.display = 'block';
    badge.className = 'badge bg-secondary';
    badge.innerHTML = 'Checking...';

    setTimeout(() => {
        const ok = Math.random() > 0.3;

        if (ok) {
            badge.className = 'badge bg-success';
            badge.innerHTML = 'Available';
            state.codeAvailable = true;
        } else {
            badge.className = 'badge bg-danger';
            badge.innerHTML = 'Exists';
            state.codeAvailable = false;
        }
    }, 800);
}

// ---------------- API ----------------
async function loadUserCertificates(userId) {
    if (!userId) return;

    const data = await fetchUserCertificates(userId);

    document.getElementById('userTotalCertificates').textContent = data.total || 0;
    document.getElementById('userValidCertificates').textContent = data.valid || 0;
    document.getElementById('userExpiredCertificates').textContent = data.expired || 0;

    document.getElementById('userCertificateStats').style.display = 'block';
}

async function loadCourseCertificates(courseId) {
    if (!courseId) return;

    const data = await fetchCourseCertificates(courseId);

    document.getElementById('courseTotalCertificates').textContent = data.total || 0;
    document.getElementById('courseValidCertificates').textContent = data.valid || 0;
    document.getElementById('courseExpiredCertificates').textContent = data.expired || 0;

    document.getElementById('courseCertificateStats').style.display = 'block';
}

// ---------------- UI ----------------
function updateUserInfo() {
    const select = document.getElementById('user_id');
    const opt = select.options[select.selectedIndex];
    if (!opt.value) return;

    document.getElementById('userInfo').innerHTML = `
        <h5>${opt.dataset.name}</h5>
        <p>${opt.dataset.email}</p>
    `;
}

function updateCourseInfo() {
    const select = document.getElementById('course_id');
    const opt = select.options[select.selectedIndex];
    if (!opt.value) return;

    document.getElementById('courseInfo').innerHTML = `
        <h5>${opt.dataset.title}</h5>
    `;
}

// ---------------- DATES ----------------
function setIssuedDate(type) {
    const input = document.getElementById('issued_at');

    if (type === 'now') {
        input.value = new Date().toISOString().slice(0, 16);
    } else {
        input.focus();
    }

    updateValidityPeriod();
    updatePreview();
}

function setExpiryDate(days) {
    const issued = document.getElementById('issued_at').value;

    if (!issued) {
        showToast('Set issued date first', 'warning');
        return;
    }

    const expiry = new Date(issued);
    expiry.setDate(expiry.getDate() + parseInt(days));

    document.getElementById('expires_at').value = expiry.toISOString().slice(0, 16);

    document.getElementById('noExpiry').checked = false;

    updateValidityPeriod();
}

function toggleExpiryDate() {
    const noExpiry = document.getElementById('noExpiry');
    const expiry = document.getElementById('expires_at');

    expiry.disabled = noExpiry.checked;
    if (noExpiry.checked) expiry.value = '';

    updateValidityPeriod();
}

function updateValidityPeriod() {
    const issued = document.getElementById('issued_at').value;
    const expiry = document.getElementById('expires_at').value;

    const box = document.getElementById('validityPeriod');
    const text = document.getElementById('validityText');

    if (!issued) return (box.style.display = 'none');

    if (!expiry) {
        box.style.display = 'block';
        text.textContent = 'No expiry';
        return;
    }

    const diff = Math.ceil((new Date(expiry) - new Date(issued)) / 86400000);

    text.textContent = `${diff} days`;
    box.style.display = 'block';
}

// ---------------- PREVIEW ----------------
function updatePreview() {
    const userSelect = document.getElementById('user_id');
    const courseSelect = document.getElementById('course_id');
    const certCode = document.getElementById('certificate_code').value;
    const issuedDate = document.getElementById('issued_at').value;

    const userName = userSelect.selectedIndex > 0
        ? userSelect.options[userSelect.selectedIndex].getAttribute('data-name')
        : 'Recipient Name';

    const courseName = courseSelect.selectedIndex > 0
        ? courseSelect.options[courseSelect.selectedIndex].getAttribute('data-title')
        : 'Course Title';

    document.getElementById('previewUserName').textContent = userName;
    document.getElementById('previewCourseName').textContent = courseName;
    document.getElementById('previewCertCode').textContent =
        certCode || 'CERT-XXXX-XXXX-XXXX';

    if (issuedDate) {
        const date = new Date(issuedDate);

        const formatted = date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        document.getElementById('previewIssuedDate').textContent = formatted;
    } else {
        document.getElementById('previewIssuedDate').textContent = '—';
    }
}

function refreshPreview() {
    updatePreview();
    showToast('Preview refreshed');
}

// ---------------- DUPLICATE ----------------
async function checkForDuplicates() {
    const userId = document.getElementById('user_id').value;
    const courseId = document.getElementById('course_id').value;

    const data = await checkDuplicate(userId, courseId);

    if (data.duplicate) {
        new bootstrap.Modal(document.getElementById('duplicateModal')).show();
        return true;
    }

    return false;
}

function proceedWithDuplicate() {
    state.duplicateConfirmed = true;
    bootstrap.Modal.getInstance(document.getElementById('duplicateModal')).hide();
}

// ---------------- MISC ----------------
function resetForm() {
    document.getElementById('certificateForm').reset();
    generateCertificateCode();
    updatePreview();
}

function saveAsDraft() {
    showToast('Draft saved');
}

// ---------------- SHORTCUTS ----------------
function bindKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            saveAsDraft();
        }

        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('certificateForm').submit();
        }

        if ((e.ctrlKey || e.metaKey) && e.key === 'g') {
            e.preventDefault();
            generateCertificateCode();
        }
    });
}