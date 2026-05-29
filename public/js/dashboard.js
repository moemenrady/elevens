const Dashboard = {
    csrfToken: document.querySelector('meta[name="csrf-token"]').content,

    // 1. إدارة الـ Drawer
    toggleDrawer: function() {
        document.body.classList.toggle('drawer-open');
    },

    // 2. تحميل الأقسام (AJAX)
    loadSection: function(sectionName, el = null) {
        const contentArea = document.getElementById('mainContent');
        
        // إغلاق الـ Drawer وتحديث الـ Active Tab
        document.body.classList.remove('drawer-open');
        document.querySelectorAll('.nav-link, .tab-btn').forEach(link => link.classList.remove('active'));
        if (el) el.classList.add('active');

        contentArea.style.opacity = '0.5';

        fetch(`/dashboard/section/${sectionName}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(html => {
                contentArea.innerHTML = html;
                contentArea.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                this.showSnackbar("حدث خطأ أثناء جلب البيانات ❌");
                contentArea.style.opacity = '1';
            });
    },

    // 3. إدارة الـ Snackbar
    showSnackbar: function(message) {
        const bar = document.getElementById('snackbar');
        bar.innerText = message;
        bar.style.display = 'block';
        setTimeout(() => { bar.style.display = 'none'; }, 3000);
    },

    // 4. إدارة الـ Modals
    openModal: function(modalId) {
        document.getElementById(modalId).classList.add('active');
    },
    closeModal: function(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }
};

// 5. إدارة كل الـ Forms بشكل ديناميكي (Global Form Handler)
document.addEventListener("submit", function(e) {
    // التحقق إذا كان الفورم يحمل كلاس ajax-form
    if (e.target.classList.contains("ajax-form")) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const url = form.getAttribute('action');
        const method = form.getAttribute('method') || 'POST';
        const sectionToReload = form.getAttribute('data-section'); // لمعرفة أي قسم نعمله Reload

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': Dashboard.csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error("Error in request");
            return res.json();
        })
        .then(data => {
            Dashboard.showSnackbar(data.message || "تمت العملية بنجاح 🔥");
            
            // إغلاق المودال إذا كان الفورم داخل مودال
            const modal = form.closest('.glass-modal-overlay-simple, .glass-modal-overlay');
            if (modal) modal.classList.remove('active'); // أو window.location.hash = "" بناءً على طريقتك

            // إعادة تحميل القسم
            if (sectionToReload) {
                Dashboard.loadSection(sectionToReload);
            }
        })
        .catch(err => {
            console.error(err);
            Dashboard.showSnackbar("حدث خطأ أثناء التنفيذ ❌");
        });
    }
});

// تأثير الخلفية بالماوس
document.addEventListener('mousemove', (e) => {
    const x = e.clientX / window.innerWidth;
    const y = e.clientY / window.innerHeight;
    const orb1 = document.querySelector('.orb-1');
    const orb2 = document.querySelector('.orb-2');
    if(orb1) orb1.style.transform = `translate(${x * 40}px, ${y * 40}px)`;
    if(orb2) orb2.style.transform = `translate(${x * -50}px, ${y * -50}px)`;
});