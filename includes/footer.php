<?php
// Function to get all active pages for footer links
function getActivePages() {
    global $pdo;
    $sql = "SELECT title, slug FROM pages WHERE is_active = 1 ORDER BY title ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$active_pages = getActivePages();
?>
    <!-- Premium Footer - Redesigned -->
    <footer style="background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%); color: var(--color-white); padding: 100px 0 40px; position: relative; overflow: hidden; border-top: 3px solid var(--color-red);">
        <!-- Animated Background Elements -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;
                    background:
                        radial-gradient(circle at 15% 85%, rgba(220, 38, 38, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 85% 15%, rgba(22, 163, 74, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 50% 50%, rgba(220, 38, 38, 0.08) 0%, transparent 60%);
                    z-index: 0; animation: backgroundPulse 8s ease-in-out infinite;"></div>

        <!-- Geometric Pattern Overlay -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;
                    background-image:
                        radial-gradient(circle at 25% 25%, rgba(255,255,255,0.03) 1px, transparent 1px),
                        radial-gradient(circle at 75% 75%, rgba(255,255,255,0.03) 1px, transparent 1px);
                    background-size: 50px 50px;
                    z-index: 0;"></div>

        <div class="container" style="position: relative; z-index: 1;">
            <!-- Main Footer Content -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 60px; margin-bottom: 60px;">

                <!-- School Information - Redesigned -->
                <div>
                    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding: 20px; background: rgba(220, 38, 38, 0.1); border-radius: 20px; border-left: 4px solid var(--color-green);">
                        <div style="position: relative;">
                            <img src="images/logo.jpg" alt="St. Philip Neri School Logo"
                                 style="height: 70px; width: auto; filter: drop-shadow(0 6px 12px rgba(0,0,0,0.4)); transition: transform 0.3s ease;"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNzAiIGhlaWdodD0iNzAiIHZpZXdCb3g9IjAgMCA3MCA3MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzUiIGN5PSIzNSIgcj0iMzUiIGZpbGw9InVybCgjZ3JhZGllbnQwKSIvPgo8cGF0aCBkPSJNMzUgMTVMMzUgMzVNMzUgMzVMMjUgNDVNMzUgMzVMNDUgNDUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMyIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+CjxkZWZzPgo8bGluZWFyR3JhZGllbnQgaWQ9ImdyYWRpZW50MCIgeDE9IjM1IiB5MT0iMCIgeDI9IjM1IiB5Mj0iNzAiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agb2Zmc2V0PSIwIiBzdG9wLWNvbG9yPSIjZGMyNjI2Ii8+CjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzE2YTM0YSIvPgo8L2xpbmVhckdyYWRpZW50Pgo8L2RlZnM+Cjwvc3ZnPgo='"
                                 onmouseover="this.style.transform='rotate(5deg) scale(1.05)'"
                                 onmouseout="this.style.transform='rotate(0) scale(1)'">
                            <div style="position: absolute; bottom: -5px; right: -5px; width: 20px; height: 20px; background: var(--color-green); border-radius: 50%; border: 2px solid #000;"></div>
                        </div>
                        <div>
                            <h3 style="color: var(--color-white); font-size: 1.6rem; margin-bottom: 8px; font-weight: 800; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">St. Philip Neri</h3>
                            <p style="color: var(--color-green); font-style: italic; font-size: 1rem; font-weight: 600; margin: 0; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Together we Achieve the extraordinary</p>
                        </div>
                    </div>

                    <p style="margin-bottom: 30px; line-height: 1.8; color: rgba(255, 255, 255, 0.85); font-size: 1.05rem; padding: 0 10px;">
                        Providing <span style="color: var(--color-red); font-weight: 600;">excellence in education</span> with state-of-the-art facilities, experienced faculty, and a commitment to <span style="color: var(--color-green); font-weight: 600;">holistic development</span> for every student.
                    </p>

                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <?php
                        $socialPlatforms = [
                            ['facebook', 'https://www.facebook.com/profile.php?id=100057603453033', 'fab fa-facebook-f', '#1877F2'],
                            ['instagram', 'https://www.instagram.com/philip.neri.school/', 'fab fa-instagram', '#E4405F'],
                            ['twitter', 'https://x.com/PhilipNeriSch', 'fab fa-twitter', '#1DA1F2'],
                            ['tiktok', 'https://www.tiktok.com/@st.philip.neri.sc', 'fab fa-tiktok', '#000000'],
                            ['youtube', 'https://www.youtube.com/channel/UC4suquLZlGd0DZaLMsgUo9w/', 'fab fa-youtube', '#FF0000']
                        ];

                        foreach ($socialPlatforms as $platform): ?>
                            <a href="<?php echo $platform[1]; ?>" class="social-icon <?php echo $platform[0]; ?>" target="_blank" aria-label="<?php echo ucfirst($platform[0]); ?>"
                               style="width: 50px; height: 50px; background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%);
                                      color: var(--color-white); border-radius: 12px; display: flex; align-items: center; justify-content: center;
                                      text-decoration: none; transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); backdrop-filter: blur(15px);
                                      border: 1px solid rgba(255, 255, 255, 0.15); position: relative; overflow: hidden;"
                               onmouseover="this.style.background='linear-gradient(135deg, <?php echo $platform[3]; ?>20 0%, <?php echo $platform[3]; ?>10 100%)'; this.style.transform='translateY(-5px) rotate(5deg)'; this.style.borderColor='<?php echo $platform[3]; ?>40';"
                               onmouseout="this.style.background='linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%)'; this.style.transform='translateY(0) rotate(0)'; this.style.borderColor='rgba(255,255,255,0.15)'">
                                <i class="<?php echo $platform[2]; ?>" style="font-size: 1.2rem; z-index: 2; position: relative;"></i>
                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: <?php echo $platform[3]; ?>; opacity: 0; transition: opacity 0.3s ease;"></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Links - Enhanced -->
                <div>
                    <h4 style="color: var(--color-white); margin-bottom: 30px; font-size: 1.4rem; font-weight: 800;
                               padding-bottom: 15px; border-bottom: 3px solid var(--color-red); display: inline-block; position: relative;">
                        <i class="fas fa-bolt" style="margin-right: 12px; color: var(--color-green);"></i>Quick Links
                        <span style="position: absolute; bottom: -3px; left: 0; width: 60px; height: 3px; background: var(--color-green);"></span>
                    </h4>
                    <ul style="list-style: none; display: grid; gap: 15px;">
                        <?php foreach ($active_pages as $page): ?>
                            <li style="position: relative;">
                                <a href="<?php echo $page['slug'] . '.php'; ?>"
                                   style="color: rgba(255, 255, 255, 0.85); text-decoration: none; transition: all 0.3s ease;
                                          display: flex; align-items: center; gap: 15px; padding: 12px 20px; background: rgba(255,255,255,0.03);
                                          border-radius: 10px; border-left: 3px solid transparent; font-weight: 500;"
                                   onmouseover="this.style.color='var(--color-white)'; this.style.background='rgba(220, 38, 38, 0.1)'; this.style.borderLeftColor='var(--color-green)'; this.style.transform='translateX(8px)';"
                                   onmouseout="this.style.color='rgba(255, 255, 255, 0.85)'; this.style.background='rgba(255,255,255,0.03)'; this.style.borderLeftColor='transparent'; this.style.transform='translateX(0)';">
                                    <div style="width: 8px; height: 8px; background: var(--color-red); border-radius: 50%; flex-shrink: 0;"></div>
                                    <?php echo htmlspecialchars($page['title']); ?>
                                    <i class="fas fa-arrow-right" style="margin-left: auto; font-size: 0.8rem; color: var(--color-green); opacity: 0; transition: opacity 0.3s ease;"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Contact Information - Redesigned -->
                <div>
                    <h4 style="color: var(--color-white); margin-bottom: 30px; font-size: 1.4rem; font-weight: 800;
                               padding-bottom: 15px; border-bottom: 3px solid var(--color-red); display: inline-block; position: relative;">
                        <i class="fas fa-map-marker-alt" style="margin-right: 12px; color: var(--color-green);"></i>Contact Info
                        <span style="position: absolute; bottom: -3px; left: 0; width: 60px; height: 3px; background: var(--color-green);"></span>
                    </h4>
                    <div style="display: grid; gap: 25px;">
                        <?php
                        $contactItems = [
                            ['map-marker-alt', 'Address:', 'Joska<br>Off Kangundo Road'],
                            ['phone-alt', 'Phone:', '+254 719 221 401'],
                            ['envelope', 'Email:', 'info@stphilipnerischool.sc.ke'],
                            ['clock', 'Office Hours:', 'Mon - Fri: 7:30 AM - 5:00 PM<br>Sat: 8:00 AM - 4:00 PM']
                        ];

                        foreach ($contactItems as $item): ?>
                            <div style="display: flex; align-items: flex-start; gap: 18px; padding: 15px; background: rgba(255,255,255,0.03); border-radius: 12px; transition: all 0.3s ease;"
                                 onmouseover="this.style.background='rgba(22, 163, 74, 0.1)'; this.style.transform='translateY(-3px)';"
                                 onmouseout="this.style.background='rgba(255,255,255,0.03)'; this.style.transform='translateY(0)';">
                                <div style="background: linear-gradient(135deg, var(--color-red) 0%, var(--color-green) 100%);
                                            width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center;
                                            justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                                    <i class="fas fa-<?php echo $item[0]; ?>" style="color: white; font-size: 1.1rem;"></i>
                                </div>
                                <div>
                                    <strong style="color: var(--color-red); display: block; margin-bottom: 8px; font-size: 0.95rem;"><?php echo $item[1]; ?></strong>
                                    <span style="color: rgba(255, 255, 255, 0.9); line-height: 1.6; font-size: 0.95rem;"><?php echo $item[2]; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- YouTube Video & Newsletter - Enhanced -->
                <div>
                    <h4 style="color: var(--color-white); margin-bottom: 30px; font-size: 1.4rem; font-weight: 800;
                               padding-bottom: 15px; border-bottom: 3px solid var(--color-red); display: inline-block; position: relative;">
                        <i class="fas fa-play-circle" style="margin-right: 12px; color: var(--color-green);"></i>School Highlights
                        <span style="position: absolute; bottom: -3px; left: 0; width: 60px; height: 3px; background: var(--color-green);"></span>
                    </h4>

                    <!-- Video Container -->
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 16px;
                                margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.4); border: 2px solid rgba(255,255,255,0.1); transition: all 0.3s ease;"
                         onmouseover="this.style.borderColor='var(--color-red)'; this.style.transform='translateY(-5px)';"
                         onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)';">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(45deg, var(--color-red) 0%, var(--color-green) 100%); opacity: 0.3; z-index: 1; border-radius: 14px;"></div>
                        <iframe
                            src="https://www.youtube.com/embed/e8gsxwY1i9w?si=AC6mP1yTorYYQSDg"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; z-index: 2; border-radius: 14px;"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            title="St. Philip Neri School Video">
                        </iframe>
                    </div>

                    <!-- Newsletter -->

                </div>
            </div>

            <!-- Bottom Footer - Enhanced -->
            <div style="border-top: 1px solid rgba(255, 255, 255, 0.15); padding-top: 40px; text-align: center; position: relative;">
                <!-- Decorative element -->
                <div style="position: absolute; top: -2px; left: 50%; transform: translateX(-50%); width: 100px; height: 4px; background: linear-gradient(90deg, var(--color-red) 0%, var(--color-green) 100%); border-radius: 2px;"></div>

                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 25px; margin-bottom: 30px;">
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 1rem; font-weight: 500;">
                        &copy; <?php echo date('Y'); ?> St. Philip Neri School. All rights reserved. Developed by <a href="https://housestark-fred.ct.ws">Moi Fred</a>
                    </p>
                    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                        <?php
                        $footerLinks = [
                            ['shield-alt', '../privacy-policy', 'Privacy Policy'],
                            ['file-contract', '../terms', 'Terms of Service'],
                            ['sitemap', '../admin/sitemap', 'Sitemap']
                        ];

                        foreach ($footerLinks as $link): ?>
                            <a href="<?php echo $link[1]; ?>"
                               style="color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: all 0.3s ease;
                                      display: flex; align-items: center; gap: 8px; font-weight: 500; padding: 8px 15px; border-radius: 25px;"
                               onmouseover="this.style.color='var(--color-green)'; this.style.background='rgba(22, 163, 74, 0.1)';"
                               onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'; this.style.background='transparent';">
                                <i class="fas fa-<?php echo $link[0]; ?>" style="font-size: 0.9rem;"></i> <?php echo $link[2]; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Back to Top Button - Enhanced -->
                <button onclick="scrollToTop()"
                        style="background: linear-gradient(135deg, var(--color-red) 0%, var(--color-green) 100%);
                               color: white; border: none; padding: 14px 30px; border-radius: 50px; cursor: pointer;
                               transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); font-weight: 700; margin-top: 10px;
                               box-shadow: 0 6px 20px rgba(0,0,0,0.2); position: relative; overflow: hidden;"
                        onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 25px rgba(220, 38, 38, 0.4)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.2)';">
                    <i class="fas fa-arrow-up" style="margin-right: 10px; transition: transform 0.3s ease;"></i>
                    Back to Top
                    <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s ease;"></div>
                </button>
            </div>
        </div>
    </footer>

    <!-- Enhanced Footer Scripts -->
    <script>
        // Enhanced social media hover effects
        document.querySelectorAll('.social-icon').forEach(icon => {
            icon.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) rotate(5deg) scale(1.1)';
                this.querySelector('div').style.opacity = '0.1';
            });

            icon.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) rotate(0) scale(1)';
                this.querySelector('div').style.opacity = '0';
            });
        });

        // Enhanced quick links hover effects
        document.querySelectorAll('.quick-links a').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.querySelector('i').style.opacity = '1';
                this.querySelector('i').style.transform = 'translateX(3px)';
            });

            link.addEventListener('mouseleave', function() {
                this.querySelector('i').style.opacity = '0';
                this.querySelector('i').style.transform = 'translateX(0)';
            });
        });

        // Enhanced back to top functionality
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Enhanced show/hide back to top button
        window.addEventListener('scroll', function() {
            const backToTop = document.querySelector('button[onclick="scrollToTop()"]');
            const scrollPosition = window.pageYOffset;

            if (scrollPosition > 300) {
                backToTop.style.opacity = '1';
                backToTop.style.visibility = 'visible';
                backToTop.style.transform = 'translateY(0)';
            } else {
                backToTop.style.opacity = '0';
                backToTop.style.visibility = 'hidden';
                backToTop.style.transform = 'translateY(10px)';
            }
        });

        // Initialize enhanced back to top button
        document.addEventListener('DOMContentLoaded', function() {
            const backToTop = document.querySelector('button[onclick="scrollToTop()"]');
            backToTop.style.opacity = '0';
            backToTop.style.visibility = 'hidden';
            backToTop.style.transform = 'translateY(10px)';
            backToTop.style.transition = 'all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';

            // Add shine effect on hover
            backToTop.addEventListener('mouseenter', function() {
                this.querySelector('div').style.left = '100%';
            });

            backToTop.addEventListener('mouseleave', function() {
                this.querySelector('div').style.left = '-100%';
            });
        });

        // Background animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes backgroundPulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.8; }
            }
        `;
        document.head.appendChild(style);
    </script>

    <!-- Mobile-specific styles (unchanged as requested) -->
    <style>
        @media (max-width: 768px) {
            footer .container > div:first-child {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
            }

            footer .social-links {
                justify-content: center;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .contact-item {
                justify-content: center;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            footer {
                padding: 60px 0 20px;
            }

            .footer-section {
                text-align: center;
            }

            .quick-links ul {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</body>
</html>
