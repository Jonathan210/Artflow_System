// assets/js/main.js

const BASE_URL = document.documentElement.dataset.baseUrl || '';
const CSRF_TOKEN = document.documentElement.dataset.csrf || '';

// ============================================================
// Toast Notification System
// ============================================================
const Toast = {
  init() {
    this.container = document.createElement('div');
    this.container.id = 'toast-container';
    this.container.style.cssText = `
      position: fixed;
      top: 30px;
      right: 30px;
      z-index: 10000;
      display: flex;
      flex-direction: column;
      gap: 12px;
    `;
    document.body.appendChild(this.container);
  },

  show(message, type = 'success') {
    const toast = document.createElement('div');
    const colors = {
      success: '#A5E17D',
      error: '#FF7B7B',
      info: '#C4B5A5'
    };

    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      color: #2C2420;
      padding: 16px 28px;
      border-radius: 50px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
      border-left: 6px solid ${colors[type]};
      font-weight: 600;
      font-size: 0.9rem;
      transform: translateX(100px);
      opacity: 0;
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      pointer-events: auto;
      min-width: 280px;
    `;
    
    toast.innerHTML = `
      <div style="display: flex; align-items: center; gap: 12px;">
        <span>${message}</span>
      </div>
    `;

    this.container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
      toast.style.transform = 'translateX(0)';
      toast.style.opacity = '1';
    });

    // Remove
    setTimeout(() => {
      toast.style.transform = 'translateX(100px)';
      toast.style.opacity = '0';
      setTimeout(() => toast.remove(), 500);
    }, 4000);
  }
};

// ============================================================
// Post Interactions (AJAX)
// ============================================================
function initPostActions() {
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.post-like, .post-save');
    if (!btn) return;

    const action = btn.dataset.action;
    const postCard = btn.closest('.post-card');
    if (!postCard) return;
    
    const postId = postCard.dataset.id;
    
    // Optimistic UI update
    const icon = btn.querySelector('span:not(.count)');
    const countEl = btn.querySelector('.count');
    const isActive = btn.classList.contains('active');
    
    btn.classList.toggle('active');
    if (action === 'like') {
        icon.textContent = isActive ? '🤍' : '❤️';
        if(countEl) {
            let count = parseInt(countEl.textContent) || 0;
            countEl.textContent = Math.max(0, count + (isActive ? -1 : 1));
        }
    } else {
        icon.textContent = isActive ? '☆' : '★';
    }

    try {
        const formData = new FormData();
        formData.append('post_id', postId);
        formData.append('csrf_token', CSRF_TOKEN);

        const response = await fetch(`${BASE_URL}/controllers/PostController.php?action=${action}`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        if (!data.success) {
            // Revert on failure
            btn.classList.toggle('active');
            if (action === 'like') {
                icon.textContent = isActive ? '❤️' : '🤍';
                if(countEl) {
                    let count = parseInt(countEl.textContent) || 0;
                    countEl.textContent = count + (isActive ? 1 : -1);
                }
            } else {
                icon.textContent = isActive ? '★' : '☆';
            }
            Toast.show(data.message || 'Action failed', 'error');
        } else {
            const label = action === 'like' ? (data.action === 'liked' ? 'Liked!' : 'Unliked') : (data.action === 'faved' ? 'Saved to collection' : 'Removed from collection');
            Toast.show(label, 'info');
        }
    } catch (err) {
        console.error(err);
        btn.classList.toggle('active');
        Toast.show('Network error', 'error');
    }
  });
}

// ============================================================
// Following Actions (AJAX)
// ============================================================
function initFollowActions() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.follow-btn');
        if (!btn) return;

        const userId = btn.dataset.userId;
        const isActive = btn.classList.contains('active');

        // Optimistic UI
        btn.classList.toggle('active');
        btn.textContent = isActive ? 'FOLLOW' : 'UNFOLLOW';

        try {
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('csrf_token', CSRF_TOKEN);

            const response = await fetch(`${BASE_URL}/controllers/UserController.php?action=follow`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            if (!data.success) {
                btn.classList.toggle('active');
                btn.textContent = isActive ? 'UNFOLLOW' : 'FOLLOW';
                Toast.show(data.message || 'Action failed', 'error');
            } else {
                Toast.show(data.action === 'followed' ? 'Now following artist' : 'Unfollowed artist', 'info');
            }
        } catch (err) {
            btn.classList.toggle('active');
            btn.textContent = isActive ? 'UNFOLLOW' : 'FOLLOW';
            Toast.show('Network error', 'error');
        }
    });
}

// ============================================================
// Interactive Inputs
// ============================================================
function initInputs() {
  const inputs = document.querySelectorAll('.share-input, .share-content, .form-control, .nav-search input');
  inputs.forEach(input => {
    input.addEventListener('focus', () => {
      const parent = input.closest('.share-progress, .form-group, .nav-search');
      if (parent) {
        parent.style.transform = 'translateY(-2px)';
        parent.style.transition = '0.3s ease';
      }
    });
    input.addEventListener('blur', () => {
      const parent = input.closest('.share-progress, .form-group, .nav-search');
      if (parent) {
        parent.style.transform = '';
      }
    });
  });
}

// ============================================================
// Password Visibility Toggle
// ============================================================
function initPasswordToggles() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.toggle-password');
        if (!btn) return;

        const targetId = btn.dataset.target;
        const input = document.getElementById(targetId);
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = 'HIDE';
            btn.style.opacity = '1';
        } else {
            input.type = 'password';
            btn.textContent = 'SHOW';
            btn.style.opacity = '0.6';
        }
    });
}

// ============================================================
// Init All
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
  Toast.init();
  initPostActions();
  initFollowActions();
  initInputs();
  initPasswordToggles();
  
  // Staggered entrance animation for cards
  const cards = document.querySelectorAll('.post-card');
  cards.forEach((card, index) => {
    card.style.animationDelay = `${index * 0.1}s`;
  });
});