Alpine.data("ProductRating", ({ rating_percent, reviews }) => ({
    ratingPercent: rating_percent,
    reviewCount: reviews?.length,
    orderProductCount: 0, // Add this line

    get hasReviewCount() {
        return this.reviewCount !== undefined;
    },
}));
