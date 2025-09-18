<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;
use Carbon\Carbon;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $blogs = [
            [
                'title' => 'The Future of Influencer Marketing in 2025',
                'short_description' => 'Explore the latest trends and predictions for influencer marketing as we look ahead to 2025 and beyond.',
                'content' => '<h2>Introduction</h2><p>Influencer marketing continues to evolve at a rapid pace. As we look toward 2025, several key trends are shaping the industry landscape.</p><h3>Key Trends</h3><ul><li>Micro-influencers gaining more importance</li><li>Authenticity over follower count</li><li>Long-term partnerships</li><li>Video content dominance</li></ul><p>The future of influencer marketing lies in building genuine connections between brands and their audiences through trusted voices.</p>',
                'author_name' => 'Marketing Team',
                'meta_title' => 'Future of Influencer Marketing 2025 | QuickFluence',
                'meta_description' => 'Discover the latest trends and predictions for influencer marketing in 2025. Learn how to stay ahead in the evolving digital landscape.',
                'meta_keywords' => 'influencer marketing, 2025 trends, digital marketing, social media',
                'status' => 1,
                'published_at' => Carbon::now(),
            ],
            [
                'title' => 'How to Choose the Right Influencer for Your Brand',
                'short_description' => 'A comprehensive guide to selecting influencers who align with your brand values and can deliver authentic engagement.',
                'content' => '<h2>Understanding Your Brand Goals</h2><p>Before reaching out to influencers, it\'s crucial to understand what you want to achieve with your campaign.</p><h3>Key Factors to Consider</h3><ol><li><strong>Audience Alignment:</strong> Does their audience match your target demographic?</li><li><strong>Engagement Rate:</strong> Look beyond follower count to actual engagement</li><li><strong>Content Quality:</strong> Review their previous content for brand fit</li><li><strong>Authenticity:</strong> Ensure their values align with your brand</li></ol><p>Remember, the right influencer partnership can significantly impact your brand\'s reach and credibility.</p>',
                'author_name' => 'Strategy Team',
                'meta_title' => 'How to Choose the Right Influencer | Brand Partnership Guide',
                'meta_description' => 'Learn how to select the perfect influencer for your brand with our comprehensive guide covering audience alignment, engagement, and authenticity.',
                'meta_keywords' => 'influencer selection, brand partnerships, marketing strategy, social media influencers',
                'status' => 1,
                'published_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'Measuring ROI in Influencer Marketing Campaigns',
                'short_description' => 'Learn how to effectively track and measure the return on investment for your influencer marketing campaigns.',
                'content' => '<h2>Setting Up Proper Tracking</h2><p>Measuring ROI in influencer marketing requires setting up proper tracking mechanisms from the start of your campaign.</p><h3>Key Metrics to Track</h3><ul><li>Reach and Impressions</li><li>Engagement Rate</li><li>Click-through Rate</li><li>Conversion Rate</li><li>Cost per Acquisition</li><li>Brand Awareness Lift</li></ul><h3>Tools for Measurement</h3><p>Utilize analytics tools, UTM parameters, and dedicated tracking links to gather comprehensive data about your campaign performance.</p><blockquote>\"What gets measured gets managed\" - This principle is especially true in influencer marketing.</blockquote>',
                'author_name' => 'Analytics Team',
                'meta_title' => 'Measuring ROI in Influencer Marketing | Campaign Analytics',
                'meta_description' => 'Master the art of measuring ROI in influencer marketing. Learn key metrics, tracking methods, and tools for campaign success.',
                'meta_keywords' => 'influencer marketing ROI, campaign measurement, marketing analytics, performance tracking',
                'status' => 1,
                'published_at' => Carbon::now()->subDays(7),
            ],
            [
                'title' => 'Building Long-term Influencer Relationships',
                'short_description' => 'Discover strategies for developing lasting partnerships with influencers that benefit both your brand and content creators.',
                'content' => '<h2>The Value of Long-term Partnerships</h2><p>While one-off campaigns can be effective, building long-term relationships with influencers offers numerous advantages.</p><h3>Benefits of Long-term Partnerships</h3><ul><li>Deeper brand understanding</li><li>More authentic content creation</li><li>Better audience trust</li><li>Cost efficiency over time</li><li>Consistent brand messaging</li></ul><h3>How to Nurture Relationships</h3><ol><li>Regular communication</li><li>Fair compensation</li><li>Creative freedom</li><li>Mutual respect</li><li>Feedback and collaboration</li></ol><p>Remember, successful influencer relationships are built on mutual trust and respect, not just transactional exchanges.</p>',
                'author_name' => 'Partnership Team',
                'meta_title' => 'Building Long-term Influencer Relationships | Partnership Strategy',
                'meta_description' => 'Learn how to build lasting influencer partnerships that drive authentic engagement and long-term brand success.',
                'meta_keywords' => 'influencer relationships, long-term partnerships, brand collaboration, influencer management',
                'status' => 1,
                'published_at' => Carbon::now()->subDays(10),
            ],
            [
                'title' => 'The Rise of Micro-Influencers: Why Smaller Can Be Better',
                'short_description' => 'Explore why micro-influencers are becoming increasingly valuable for brands seeking authentic engagement and targeted reach.',
                'content' => '<h2>What Are Micro-Influencers?</h2><p>Micro-influencers typically have between 1,000 to 100,000 followers and often boast higher engagement rates than their macro counterparts.</p><h3>Advantages of Micro-Influencers</h3><ul><li><strong>Higher Engagement Rates:</strong> Often 2-3x higher than macro-influencers</li><li><strong>More Affordable:</strong> Better ROI for smaller budgets</li><li><strong>Niche Audiences:</strong> Highly targeted demographics</li><li><strong>Authenticity:</strong> More genuine connections with followers</li><li><strong>Flexibility:</strong> Easier to work with and adapt content</li></ul><h3>When to Choose Micro-Influencers</h3><p>Micro-influencers are ideal for brands looking to target specific niches, build authentic relationships, or maximize engagement within a limited budget.</p>',
                'author_name' => 'Research Team',
                'meta_title' => 'The Rise of Micro-Influencers | Why Smaller Can Be Better',
                'meta_description' => 'Discover why micro-influencers are becoming the go-to choice for brands seeking authentic engagement and better ROI.',
                'meta_keywords' => 'micro-influencers, influencer marketing, engagement rates, niche marketing, authentic marketing',
                'status' => 0, // This one is inactive for testing
                'published_at' => null,
            ]
        ];

        foreach ($blogs as $blogData) {
            $blogData['slug'] = Blog::generateUniqueSlug($blogData['title']);
            $blogData['created_by'] = 1; // Assuming admin user ID is 1
            Blog::create($blogData);
        }
    }
}
