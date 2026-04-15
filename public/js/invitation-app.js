(function () {
  "use strict";

  const doc = document;
  const html = doc.documentElement;

  function getStoredTheme() {
    return localStorage.getItem("invite_theme") || "light";
  }

  function applyTheme(theme) {
    html.setAttribute("data-bs-theme", theme === "dark" ? "dark" : "light");
    localStorage.setItem("invite_theme", theme === "dark" ? "dark" : "light");
    doc.querySelectorAll("[data-theme-toggle]").forEach(function (btn) {
      var icon = btn.querySelector("i");
      if (icon) {
        icon.className = theme === "dark" ? "bi bi-sun-fill" : "bi bi-moon-stars-fill";
      }
    });
  }

  function toggleTheme() {
    var next = html.getAttribute("data-bs-theme") === "dark" ? "light" : "dark";
    applyTheme(next);
  }

  function showToast(message, variant) {
    variant = variant || "info";
    var iconMap = {
      primary: "info",
      secondary: "info",
      success: "success",
      danger: "error",
      error: "error",
      warning: "warning",
      info: "info",
    };
    var icon = iconMap[variant] || "info";

    if (window.Swal && typeof window.Swal.fire === "function") {
      window.Swal.fire({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3200,
        timerProgressBar: true,
        icon: icon,
        title: String(message || "Notifikasi"),
      });
      return;
    }

    window.alert(String(message || "Notifikasi"));
  }

  function confirmAction(message, title, confirmText) {
    message = message || "Apakah Anda yakin?";
    title = title || "Konfirmasi";
    confirmText = confirmText || "Ya, lanjutkan";

    if (window.Swal && typeof window.Swal.fire === "function") {
      return window.Swal.fire({
        title: title,
        text: message,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: "Batal",
        reverseButtons: true,
      }).then(function (result) {
        return !!(result && result.isConfirmed);
      });
    }

    return Promise.resolve(window.confirm(message));
  }

  function wireConfirmForms() {
    doc.querySelectorAll("form[data-confirm]").forEach(function (form) {
      if (form.dataset.confirmInited === "1") return;
      form.dataset.confirmInited = "1";

      form.addEventListener("submit", function (e) {
        if (form.dataset.confirmSkip === "1") {
          form.dataset.confirmSkip = "0";
          return;
        }

        e.preventDefault();
        var message = form.getAttribute("data-confirm") || "Apakah Anda yakin?";
        var title = form.getAttribute("data-confirm-title") || "Konfirmasi";
        var confirmText = form.getAttribute("data-confirm-confirm-text") || "Ya, lanjutkan";

        confirmAction(message, title, confirmText).then(function (ok) {
          if (!ok) return;
          form.dataset.confirmSkip = "1";
          form.submit();
        });
      });
    });
  }

  function setPageLoader(active) {
    var loader = doc.getElementById("pageLoader");
    if (!loader) return;
    loader.classList.toggle("is-active", !!active);
  }

  function initScrollFade() {
    var nodes = doc.querySelectorAll(".section-fade");
    if (!("IntersectionObserver" in window) || !nodes.length) {
      nodes.forEach(function (n) {
        n.classList.add("is-visible");
      });
      return;
    }
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            e.target.classList.add("is-visible");
            io.unobserve(e.target);
          }
        });
      },
      { threshold: 0.12 }
    );
    nodes.forEach(function (n) {
      io.observe(n);
    });
  }

  function wireDemoLogin() {
    var form = doc.getElementById("demoLoginForm");
    if (!form) return;
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (!form.checkValidity()) {
        form.classList.add("was-validated");
        return;
      }
      var email = (form.querySelector('[name="email"]') || {}).value || "";
      if (email.toLowerCase().includes("gagal")) {
        window.location.href =
          "/flow-failed?title=Login+gagal&message=Email+atau+kata+sandi+tidak+sesuai.&back=" +
          encodeURIComponent("/login");
        return;
      }
      setPageLoader(true);
      setTimeout(function () {
        window.location.href = "/app/dashboard";
      }, 650);
    });
  }

  function wireDemoRegister() {
    var form = doc.getElementById("demoRegisterForm");
    if (!form) return;
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (!form.checkValidity()) {
        form.classList.add("was-validated");
        return;
      }
      var p1 = (form.querySelector('[name="password"]') || {}).value;
      var p2 = (form.querySelector('[name="password_confirmation"]') || {}).value;
      if (p1 !== p2) {
        showToast("Konfirmasi password tidak cocok.", "danger");
        return;
      }
      showToast("Akun berhasil dibuat (demo). Mengarahkan ke login…", "success");
      setTimeout(function () {
        window.location.href = "/login";
      }, 900);
    });
  }

  function wirePaymentDemo() {
    var btnOk = doc.getElementById("btnPaymentSuccess");
    var btnFail = doc.getElementById("btnPaymentFail");
    if (btnOk) {
      btnOk.addEventListener("click", function () {
        setPageLoader(true);
        setTimeout(function () {
          window.location.href = "/app/order/status";
        }, 600);
      });
    }
    if (btnFail) {
      btnFail.addEventListener("click", function () {
        window.location.href =
          "/flow-failed?title=Pembayaran+gagal&message=Verifikasi+gagal.+Silakan+coba+lagi.&back=" +
          encodeURIComponent("/app/payment");
      });
    }
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function wireCatalogModal() {
    doc.querySelectorAll("[data-invite-preview]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var title = btn.getAttribute("data-title") || "Preview";
        var cover = btn.getAttribute("data-cover") || "";
        var body = doc.getElementById("catalogPreviewBody");
        var titleEl = doc.getElementById("catalogPreviewTitle");
        if (titleEl) titleEl.textContent = title;
        if (body) {
          var safeTitle = escapeHtml(title);
          var img =
            cover &&
            '<img src="' +
            cover.replace(/"/g, "") +
            '" alt="" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">';
          body.innerHTML =
            '<div class="ratio ratio-9x16 preview-phone bg-light-subtle">' +
            '<div class="position-relative w-100 h-100">' +
            (img || "") +
            '<div class="position-absolute bottom-0 start-0 end-0 top-0 d-flex flex-column justify-content-end p-4 text-white" style="background: linear-gradient(180deg, rgba(0,0,0,0) 35%, rgba(0,0,0,.62)); z-index: 1;">' +
            '<div class="small text-uppercase opacity-75">The Wedding of</div>' +
            '<h4 class="fw-semibold mb-1">' +
            safeTitle +
            "</h4>" +
            '<div class="small opacity-75">Sabtu, 12 Juli 2026</div>' +
            "</div></div></div>";
        }
      });
    });
  }

  function wireOrderSteps() {
    var form = doc.getElementById("orderWizardForm");
    if (!form) return;
    var steps = Array.prototype.slice.call(form.querySelectorAll("[data-step]"));
    var idx = 0;

    function showStep(i) {
      idx = Math.max(0, Math.min(steps.length - 1, i));
      steps.forEach(function (panel, j) {
        panel.classList.toggle("d-none", j !== idx);
      });
      doc.querySelectorAll("[data-step-indicator]").forEach(function (dot, j) {
        dot.classList.toggle("active", j === idx);
      });
      var nextBtn = doc.getElementById("orderNextBtn");
      if (nextBtn) nextBtn.textContent = idx === steps.length - 1 ? "Simpan & lanjut" : "Lanjut";
    }

    var next = doc.getElementById("orderNextBtn");
    var prev = doc.getElementById("orderPrevBtn");
    function validateStep(panel) {
      var controls = panel.querySelectorAll("input,select,textarea");
      var ok = true;
      controls.forEach(function (el) {
        if (!el.checkValidity()) ok = false;
      });
      if (!ok) form.classList.add("was-validated");
      return ok;
    }

    function validateAllSteps() {
      var ok = true;
      steps.forEach(function (panel) {
        if (!validateStep(panel)) ok = false;
      });
      return ok;
    }

    if (next) {
      next.addEventListener("click", function () {
        var panel = steps[idx];
        if (!validateStep(panel)) return;
        if (idx < steps.length - 1) {
          showStep(idx + 1);
        } else {
          if (!validateAllSteps()) return;
          if (form.action && form.action.indexOf("#") === -1) {
            setPageLoader(true);
            form.submit();
            return;
          }
          showToast("Data tersimpan (demo). Lanjut checkout.", "success");
          setTimeout(function () {
            window.location.href = "/app/checkout";
          }, 500);
        }
      });
    }
    if (prev) {
      prev.addEventListener("click", function () {
        showStep(idx - 1);
      });
    }
    showStep(0);
  }

  function silenceDataTablesWarnings() {
    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.dataTable) {
      window.jQuery.fn.dataTable.ext.errMode = "none";
    }
    if (typeof window.DataTable !== "undefined" && window.DataTable.ext) {
      window.DataTable.ext.errMode = "none";
    }
  }

  function hasMalformedTableRows(table) {
    var expectedColumns = table.querySelectorAll("thead th").length;
    if (!expectedColumns) return true;

    var rows = table.querySelectorAll("tbody tr");
    if (!rows.length) return false;

    for (var i = 0; i < rows.length; i++) {
      var row = rows[i];
      if (row.querySelector("td[colspan], th[colspan], td[rowspan], th[rowspan]")) {
        return true;
      }

      var cells = row.querySelectorAll(":scope > td, :scope > th");
      if (cells.length !== expectedColumns) {
        return true;
      }
    }

    return false;
  }

  function initDataTables() {
    doc.querySelectorAll("table.js-datatable").forEach(function (table) {
      if (table.dataset.dtInited === "1") return;

      if (hasMalformedTableRows(table)) {
        return;
      }

      var pageLength = Number(table.dataset.pageLength || 10);
      
      // Detect date columns by looking for data-order-by attributes
      var dateColumns = [];
      var headers = table.querySelectorAll("thead th");
      headers.forEach(function (th, idx) {
        if (th.getAttribute("data-type") === "date" || th.getAttribute("data-order-by")) {
          dateColumns.push(idx);
        }
      });
      
      var options = {
        pageLength: Number.isFinite(pageLength) && pageLength > 0 ? pageLength : 10,
        lengthMenu: [5, 10, 25, 50],
        order: [], // Will be set per table
        language: {
          search: "Cari:",
          lengthMenu: "Tampilkan _MENU_ data",
          info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
          infoEmpty: "Belum ada data",
          zeroRecords: "Data tidak ditemukan",
          paginate: {
            first: "Awal",
            last: "Akhir",
            next: "Berikutnya",
            previous: "Sebelumnya",
          },
        },
        columnDefs: [
          {
            targets: dateColumns,
            type: "date",
            render: function (data, type, row) {
              if (type === "sort" || type === "filter") {
                // Extract ISO date from data-order-by attribute if available
                var cell = row.cells ? row.cells[0] : null;
                if (cell) {
                  var orderBy = cell.getAttribute("data-order-by");
                  if (orderBy) return orderBy;
                }
              }
              return data;
            }
          }
        ]
      };
      
      // Set default order for tables with date columns
      if (dateColumns.length > 0) {
        options.order = [[dateColumns[0], "desc"]];
      }

      if (window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable) {
        window.jQuery(table).DataTable(options);
        table.dataset.dtInited = "1";
        return;
      }

      if (typeof window.DataTable !== "undefined") {
        new window.DataTable(table, options);
        table.dataset.dtInited = "1";
      }
    });
  }

  doc.addEventListener("DOMContentLoaded", function () {
    applyTheme(getStoredTheme());
    doc.querySelectorAll("[data-theme-toggle]").forEach(function (b) {
      b.addEventListener("click", toggleTheme);
    });
    initScrollFade();
    wireDemoLogin();
    wireDemoRegister();
    wirePaymentDemo();
    wireCatalogModal();
    wireOrderSteps();
    silenceDataTablesWarnings();
    initDataTables();
    wireConfirmForms();

    doc.querySelectorAll("[data-demo-toast]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        showToast(btn.getAttribute("data-demo-toast") || "Berhasil", "success");
      });
    });
  });

  window.InviteUI = {
    showToast: showToast,
    confirmAction: confirmAction,
    setPageLoader: setPageLoader,
  };
})();
