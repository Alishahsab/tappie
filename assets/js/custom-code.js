

//////////////////////my-custom-js//////////////////////
	document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const closeBtnInside = document.getElementById('sidebar-close-inside');
    const overlay = document.getElementById('sidebar-overlay');

    // Open sidebar
    toggleBtn?.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    });

    // Close with X button
    closeBtnInside?.addEventListener('click', function() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });

    // Close with overlay click
    overlay.addEventListener('click', function() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
});

document.getElementById('copy-btn').addEventListener('click', function() {
    const copyText = document.getElementById('user-link');
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value)
       
});
////////////////////////readmore/////////////////
document.addEventListener('DOMContentLoaded', function () {
    // Handle all "Read more" buttons on the page (works in dashboard, preview, public view, mobile, etc.)
    document.querySelectorAll('.bio-container .read-more-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const container = this.closest('.bio-container');
            const shortBio = container.querySelector('.bio-short');
            const fullBio = container.querySelector('.bio-full');

            if (shortBio && fullBio) {
                // Toggle visibility
                shortBio.classList.toggle('hidden');
                fullBio.classList.toggle('hidden');

                // Change button text
                if (button.textContent.trim() === 'Read more') {
                    button.textContent = 'Read less';
                } else {
                    button.textContent = 'Read more';
                }
            }
        });
    });
});
////////////////////////edit-profile-page//////////////////////////////
 

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const closeBtn = document.getElementById('sidebar-close-inside');
    const overlay = document.getElementById('sidebar-overlay');

    // Sidebar toggle for mobile
    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    });

    closeBtn.addEventListener('click', function() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });

    overlay.addEventListener('click', function() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });

    // Build bio preview HTML
    function buildBioHTML(bio) {
        if (!bio.trim()) return '';
        const limit = 120;
        const shortBio = bio.length > limit ? bio.substring(0, limit) : bio;
        const needsReadMore = bio.length > limit;

        let html = '<span class="bio-short inline">' + shortBio;
        if (needsReadMore) html += '<span class="text-blue-600 font-medium">...</span>';
        html += '</span>';

        if (needsReadMore) {
            html += '<span class="bio-full hidden">' + bio + '</span>';
            html += ' <button type="button" class="read-more-btn text-blue-600 font-medium ml-1 hover:underline focus:outline-none cursor-pointer">Read more</button>';
        }
        return html;
    }

    // Update all previews
    function updatePreview() {
        const fname = document.getElementById('input-fname').value.trim();
        const lname = document.getElementById('input-lname').value.trim();
        const fullName = (fname + ' ' + lname).trim() || '<?php echo esc_js(wp_get_current_user()->user_login); ?>';

        document.getElementById('desktop-preview-name').textContent = fullName;
        document.getElementById('mobile-preview-name').textContent = fullName;

        const location = document.getElementById('input-location').value.trim();
        document.getElementById('desktop-preview-location').textContent = location;
        document.getElementById('mobile-preview-location').textContent = location;

        const phone = document.getElementById('input-phone').value.trim();
        const phoneLink = document.getElementById('desktop-preview-phone');
        if (phone) {
            phoneLink.textContent = ' ' + phone;
            phoneLink.href = 'tel:' + phone.replace(/[^\d]/g, '');
            phoneLink.style.display = 'flex';
        } else {
            phoneLink.style.display = 'none';
        }

        // Bio preview
        const bio = document.getElementById('input-bio').value.trim();
        ['desktop', 'mobile'].forEach(prefix => {
            const container = document.getElementById(prefix + '-preview-bio-container');
            const bioDiv = container.querySelector('.bio-container');
            if (bio) {
                bioDiv.innerHTML = buildBioHTML(bio);
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        });

        // Re-attach read more buttons
        document.querySelectorAll('.read-more-btn').forEach(btn => {
            btn.onclick = function() {
                const container = this.closest('.bio-container');
                const shortBio = container.querySelector('.bio-short');
                const fullBio = container.querySelector('.bio-full');
                if (fullBio.classList.contains('hidden')) {
                    shortBio.classList.add('hidden');
                    fullBio.classList.remove('hidden');
                    this.textContent = 'Read less';
                } else {
                    shortBio.classList.remove('hidden');
                    fullBio.classList.add('hidden');
                    this.textContent = 'Read more';
                }
            };
        });
    }

    // Live input listeners
    ['input-fname', 'input-lname', 'input-location', 'input-phone', 'input-bio'].forEach(id => {
        document.getElementById(id).addEventListener('input', updatePreview);
    });

    // Profile image preview
    document.getElementById('tappie-prifleimg').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById('preview-img').src = ev.target.result;
                document.getElementById('desktop-preview-img').src = ev.target.result;
                document.getElementById('mobile-preview-img').src = ev.target.result;
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });
// Social links management
const addBtn = document.getElementById('add-social-btn');
const newForm = document.getElementById('new-social-form');
const cancelBtn = document.getElementById('cancel-new-social');
const saveBtn = document.getElementById('save-new-social');
const platformSelect = document.getElementById('social-platform-select');
const usernameInput = document.getElementById('social-username');
const labelInput = document.getElementById('social-label');

addBtn.addEventListener('click', () => newForm.classList.toggle('hidden'));

cancelBtn.addEventListener('click', () => {
    newForm.classList.add('hidden');
    usernameInput.value = '';
    labelInput.value = '';
    platformSelect.value = '';
});

saveBtn.addEventListener('click', function () {
    if (!platformSelect.value || !usernameInput.value.trim()) {
        alert('Please select a platform and enter a username/link');
        return;
    }

    const selectedOption = platformSelect.options[platformSelect.selectedIndex];
    const iconUrl = selectedOption.dataset.icon || '';
    const key = platformSelect.value;
    const postid = selectedOption.dataset.postid || '';
    const label = labelInput.value.trim() || selectedOption.textContent.trim();
    const username = usernameInput.value.trim();

    let iconHTML = '<i class="fas fa-link text-gray-500"></i>';
    if (iconUrl) iconHTML = `<img src="${iconUrl}" class="!w-12 !h-12 object-contain" alt="">`;

    const container = document.getElementById('social-links-container');
    const index = container.children.length;

    const newItem = document.createElement('div');
    newItem.className =
        'social-item flex items-center gap-3 p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 group';
    newItem.dataset.index = index;

    newItem.innerHTML = `
        <div class="cursor-move text-gray-400 hover:text-gray-700 flex-shrink-0">
            <i class="fas fa-grip-vertical"></i>
        </div>

        <div class="flex-1 flex items-center gap-3 view-mode">
            <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">${iconHTML}</div>
            <div class="flex-1 min-w-0">
                <div class="font-medium truncate">${label}</div>
                <div class="text-sm text-gray-500 truncate">${username}</div>
            </div>
        </div>

        <div class="hidden edit-mode flex-1 flex items-center gap-3">
            <input type="text" class="edit-label w-32 px-2 py-1 border rounded" value="${label}">
            <input type="text" class="edit-url flex-1 px-2 py-1 border rounded" value="${username}">
        </div>

        <div class="flex gap-2 flex-shrink-0">
            <button type="button" class="edit-social text-blue-600 hover:text-blue-800   transition">
                <i class="fas fa-edit text-gray-400"></i>
            </button>
            <button type="button" class="delete-social text-red-600 hover:text-red-800   transition">
                <i class="fas fa-trash-alt text-gray-400"></i>
            </button>
            <button type="button" class="save-edit hidden text-green-600 hover:text-green-800">
                <i class="fas fa-check"></i>
            </button>
            <button type="button" class="cancel-edit hidden text-gray-600 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <input type="hidden" name="social_links[${index}][key]" value="${key}">
        <input type="hidden" name="social_links[${index}][url]" value="${username}">
        <input type="hidden" name="social_links[${index}][label]" value="${label}">
        <input type="hidden" name="social_links[${index}][id]" value="${postid}">
    `;

    // ✅ ONLY CHANGE: hide EDIT button for contact
    if (key === 'contact') {
        newItem.querySelector('.edit-social')?.remove();
    }

    container.appendChild(newItem);
    newForm.classList.add('hidden');
    usernameInput.value = '';
    labelInput.value = '';
    platformSelect.value = '';

    initSortable();
    updateAllSocialPreviews();
});

// Click handling
document.addEventListener('click', function (e) {
    const item = e.target.closest('.social-item');
    const key = item?.querySelector('input[name*="[key]"]')?.value;

    // ⛔ block edit click for contact
    if (key === 'contact' && e.target.closest('.edit-social')) return;

    const deleteBtn = e.target.closest('.delete-social');
    const editBtn = e.target.closest('.edit-social');
    const saveBtn = e.target.closest('.save-edit');
    const cancelBtn = e.target.closest('.cancel-edit');

    if (deleteBtn) {
        if (confirm('Remove this social link?')) {
            deleteBtn.closest('.social-item').remove();
            updateIndexes();
            updateAllSocialPreviews();
        }
    }

    if (editBtn) {
        const item = editBtn.closest('.social-item');
        item.querySelector('.view-mode').classList.add('hidden');
        item.querySelector('.edit-mode').classList.remove('hidden');
        item.querySelector('.edit-social').classList.add('hidden');
        item.querySelector('.delete-social').classList.add('hidden');
        item.querySelector('.save-edit').classList.remove('hidden');
        item.querySelector('.cancel-edit').classList.remove('hidden');
    }

    if (saveBtn || cancelBtn) {
        const item = (saveBtn || cancelBtn).closest('.social-item');
        const isSave = !!saveBtn;

        if (isSave) {
            const labelInput = item.querySelector('.edit-label');
            const urlInput = item.querySelector('.edit-url');
            if (!urlInput.value.trim()) {
                alert('URL cannot be empty');
                return;
            }
            item.querySelector('.font-medium').textContent = labelInput.value.trim();
            item.querySelector('.text-sm').textContent = urlInput.value.trim();
            item.querySelector('input[name*="label"]').value = labelInput.value.trim();
            item.querySelector('input[name*="url"]').value = urlInput.value.trim();
            updateAllSocialPreviews();
        }

        item.querySelector('.view-mode').classList.remove('hidden');
        item.querySelector('.edit-mode').classList.add('hidden');
        item.querySelector('.edit-social')?.classList.remove('hidden');
        item.querySelector('.delete-social')?.classList.remove('hidden');
        item.querySelector('.save-edit').classList.add('hidden');
        item.querySelector('.cancel-edit').classList.add('hidden');
    }
});
function updateAllSocialPreviews() {
    const desktopContainer = document.getElementById('desktop-preview-socials');
    desktopContainer.innerHTML = '';

    document.querySelectorAll('#social-links-container .social-item').forEach(item => {

        const key = item.querySelector('input[name*="][key]"]')?.value || '';
        const iconHTML = item.querySelector('.view-mode .w-8.h-8')?.innerHTML || '';
        const label = item.querySelector('.view-mode .font-medium')?.childNodes[0].textContent.trim() || '';
        const urlPart = item.querySelector('.view-mode .text-sm.text-gray-500')?.textContent.trim() || '';

        const link = document.createElement('a');

        // CONTACT CONDITION
        if (key === 'contacts') {
            link.href = 'javascript:void(0)';
            link.className =
                'tapp-contact-vcf flex justify-center inline-block px-8 py-3 flex items-center bg-[#FF8686] text-white font-medium rounded-[8px] hover:bg-[#54B7B4]';

            // username injected globally from PHP
            link.setAttribute('data-userurl', window.tappUserLogin || '');
        } else {
            link.href = urlPart.startsWith('http') ? urlPart : 'https://' + urlPart;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.className =
                'flex justify-center inline-block px-8 py-3 flex items-center bg-[#FF8686] text-white font-medium rounded-[8px] hover:bg-[#54B7B4]';
        }

        link.innerHTML =
            iconHTML +
            `<span class="text-sm ms-2">${label}</span>`;

        desktopContainer.appendChild(link);
    });
}

    function initSortable() {
        if (window.socialSortable) window.socialSortable.destroy();
        window.socialSortable = new Sortable(document.getElementById('social-links-container'), {
            animation: 150,
            handle: '.cursor-move',
            onEnd: () => {
                updateIndexes();
                updateAllSocialPreviews();
            }
        });
    }

    function updateIndexes() {
        document.querySelectorAll('#social-links-container .social-item').forEach((item, index) => {
            item.dataset.index = index;
            item.querySelectorAll('input[type="hidden"]').forEach(input => {
                const name = input.name.replace(/\[\d+\]/, `[${index}]`);
                input.name = name;
            });
        });
    }

    // Initial setup
    initSortable();
    updatePreview();
    updateAllSocialPreviews();

    // Copy button
    document.getElementById('copy-btn').addEventListener('click', function() {
        const copyText = document.getElementById('user-link');
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
    });
});
//////////////public-profile//////////////////// 

document.addEventListener('DOMContentLoaded', function () {
    const socialContainer = document.querySelector('#social-links-container');
    const desktopPreview = document.querySelector('#desktop-preview-socials');

    // This value must already be printed somewhere globally from PHP
    // Example in PHP:
    // <script>window.tappUserLogin = "<?php echo esc_js($userinfo->user_login); ?>";</script>
    const userLogin = window.tappUserLogin || '';

    function updatePreviews() {
        desktopPreview.innerHTML = '';

        socialContainer.querySelectorAll('.social-item').forEach(item => {
            const key   = item.querySelector('input[name*="][key]"]').value;
            const url   = item.querySelector('input[name*="][url]"]').value;
            const label = item.querySelector('input[name*="][label]"]').value || key;

            const iconImg = item.querySelector('.view-mode img');
            const iconSrc = iconImg ? iconImg.src : '';

            const previewItem = document.createElement('a');

            // CONTACT SPECIAL CASE
            if (key === 'contacts') {
                previewItem.className =
                    'tapp-contact-vcf flex justify-center inline-block px-8 py-3 flex items-center bg-[#FF8686] !text-white font-medium rounded-[8px] hover:bg-[#54B7B4]';

                previewItem.setAttribute('data-userurl', userLogin);
                previewItem.href = 'javascript:void(0)';
            } else {
                previewItem.className =
                    'flex justify-center inline-block px-8 py-3 flex items-center bg-[#FF8686] !text-white font-medium rounded-[8px] hover:bg-[#54B7B4]';

                previewItem.href = url.startsWith('http') ? url : 'https://' + url;
                previewItem.target = '_blank';
                previewItem.rel = 'noopener noreferrer';
            }

            if (iconSrc) {
                const img = document.createElement('img');
                img.src = iconSrc;
                img.alt = label;
                img.className = 'w-[1rem] h-[1rem] object-contain me-2';
                previewItem.appendChild(img);
            }

            const text = document.createElement('span');
            text.textContent = label;
            text.className = 'text-sm text-white';
            previewItem.appendChild(text);

            desktopPreview.appendChild(previewItem);
        });
    }

    // Initial load
    updatePreviews();

    // Observe add/remove/reorder
    const observer = new MutationObserver(updatePreviews);
    observer.observe(socialContainer, { childList: true, subtree: true });

    // Button-based updates
    socialContainer.addEventListener('click', function (e) {
        if (e.target.closest('.edit-social, .save-edit, .cancel-edit, .delete-social, #save-new-social')) {
            setTimeout(updatePreviews, 100);
        }
    });
});

  
//  .....................for-profile///////////////
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('tappie-prifleimg');
    const desktopPreview = document.getElementById('desktop-preview-img'); // Used in both sidebar and right panel
    const mobilePreview = document.getElementById('mobile-preview-img');

    if (fileInput) {
        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function (event) {
                    const imageUrl = event.target.result;

                    // Update desktop/sidebar preview
                    if (desktopPreview) {
                        desktopPreview.src = imageUrl;
                    }

                    // Update mobile preview
                    if (mobilePreview) {
                        mobilePreview.src = imageUrl;
                    }

                    // Optional: also update the very top sidebar profile pic if it has the same ID
                    // (you have two elements with id="desktop-preview-img", which is invalid HTML,
                    //  but browsers will update the first one only — better to use a class instead.
                    //  But for now, this will update all with querySelectorAll)

                    // Better approach: update ALL preview images
                    document.querySelectorAll('#desktop-preview-img, #mobile-preview-img').forEach(img => {
                        img.src = imageUrl;
                    });
                };

                reader.readAsDataURL(file);
            }
        });
    }
});
// ..................dropdown............
// 
document.addEventListener("click", function (e) {
    const editBtn   = e.target.closest(".edit-social");
    const saveBtn   = e.target.closest(".save-edit");
    const cancelBtn = e.target.closest(".cancel-edit");

    if (!editBtn && !saveBtn && !cancelBtn) return;

    e.preventDefault();

    const item = e.target.closest(".social-item");
    if (!item) return;

    /* EDIT */
    if (editBtn) {
        item.querySelector(".view-mode")?.classList.add("hidden");
        item.querySelector(".edit-mode")?.classList.remove("hidden");

        editBtn.classList.add("hidden");
        item.querySelector(".delete-social")?.classList.add("hidden");
        item.querySelector(".save-edit")?.classList.remove("hidden");
        item.querySelector(".cancel-edit")?.classList.remove("hidden");
        return;
    }

    /* SAVE */
    if (saveBtn) {
        const labelInput = item.querySelector(".edit-label");
        const urlInput   = item.querySelector(".edit-url");

        if (!labelInput || !urlInput) return;

        const newLabel = labelInput.value.trim();
        const newUrl   = urlInput.value.trim();

        // update view text safely
        const labelEl = item.querySelector(".view-mode .font-medium");
        const urlEl   = item.querySelector(".view-mode .text-sm");

        if (labelEl) labelEl.childNodes[0].nodeValue = newLabel;
        if (urlEl)   urlEl.textContent = newUrl;

        // update hidden inputs (for POST)
        item.querySelector('input[name$="[label]"]')?.setAttribute("value", newLabel);
        item.querySelector('input[name$="[url]"]')?.setAttribute("value", newUrl);

        // toggle back
        item.querySelector(".edit-mode")?.classList.add("hidden");
        item.querySelector(".view-mode")?.classList.remove("hidden");

        saveBtn.classList.add("hidden");
        item.querySelector(".cancel-edit")?.classList.add("hidden");
        item.querySelector(".edit-social")?.classList.remove("hidden");
        item.querySelector(".delete-social")?.classList.remove("hidden");
        return;
    }

    /* CANCEL */
    if (cancelBtn) {
        item.querySelector(".edit-mode")?.classList.add("hidden");
        item.querySelector(".view-mode")?.classList.remove("hidden");

        cancelBtn.classList.add("hidden");
        item.querySelector(".save-edit")?.classList.add("hidden");
        item.querySelector(".edit-social")?.classList.remove("hidden");
        item.querySelector(".delete-social")?.classList.remove("hidden");
    }
});
///////////////////colorpicker/////////////
 
///////////////////textcolor////////////picker/////
 document.addEventListener('DOMContentLoaded', () => {

    function setupColorPicker(circleId, textInputId, pickerId, hiddenId, defaultColor) {
        const circle = document.getElementById(circleId);
        const textInput = document.getElementById(textInputId);
        const picker = document.getElementById(pickerId);
        const hiddenInput = document.getElementById(hiddenId);

        function applyColor(color) {
            circle.style.backgroundColor = color;
            textInput.value = color;
            hiddenInput.value = color;
        }

        const savedColor = hiddenInput.value.trim();
        if (savedColor && /^#[0-9A-Fa-f]{6}$/.test(savedColor)) {
            applyColor(savedColor);
        } else {
            applyColor(defaultColor);
        }

        // Circle click opens picker
        circle.addEventListener('click', () => picker.click());

        // Picker changes
        picker.addEventListener('input', e => applyColor(e.target.value));

        // Text input changes
        textInput.addEventListener('input', e => {
            const val = e.target.value;
            if (/^#[0-9A-Fa-f]{0,6}$/.test(val)) {
                circle.style.backgroundColor = val;
                hiddenInput.value = val;
            }
        });
    }

    // Setup both colors
    setupColorPicker('bg-color-circle', 'bg-color-text-input', 'hidden-bg-color-picker', 'tappie-bg-color-hidden', '#FF8686');
    setupColorPicker('text-color-circle', 'text-color-text-input', 'hidden-text-color-picker', 'tappie-text-color-hidden', '#000000');

});
///////////////////signup///////////////
 