'use client';

import { useState, useEffect } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Layout } from '@/components/layout/Layout';
import { blogApi, BlogPost } from '@/lib/api';
import { ArrowLeft, Calendar, User, Loader2, FileText } from 'lucide-react';

export default function BlogDetailPage() {
  const [post, setPost] = useState<BlogPost | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState('');
  
  const params = useParams();
  const router = useRouter();
  const postId = params.id as string;

  useEffect(() => {
    const fetchPost = async () => {
      try {
        setIsLoading(true);
        const data = await blogApi.getPost(postId);
        setPost(data);
      } catch (error) {
        console.error('Failed to fetch post:', error);
        setError('블로그 포스트를 불러오는데 실패했습니다.');
      } finally {
        setIsLoading(false);
      }
    };

    if (postId) {
      fetchPost();
    }
  }, [postId]);

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ko-KR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  if (isLoading) {
    return (
      <Layout>
        <div className="flex items-center justify-center min-h-[400px]">
          <Loader2 className="h-8 w-8 animate-spin" />
          <span className="ml-2">블로그 포스트를 불러오는 중...</span>
        </div>
      </Layout>
    );
  }

  if (error || !post) {
    return (
      <Layout>
        <div className="text-center py-12">
          <FileText className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <h3 className="text-lg font-semibold mb-2">포스트를 찾을 수 없습니다</h3>
          <p className="text-muted-foreground mb-4">
            {error || '요청하신 블로그 포스트가 존재하지 않습니다.'}
          </p>
          <Button onClick={() => router.push('/blog')}>
            블로그 목록으로 돌아가기
          </Button>
        </div>
      </Layout>
    );
  }

  return (
    <Layout>
      <div className="space-y-6">
        <div className="flex items-center space-x-4">
          <Button
            variant="outline"
            onClick={() => router.push('/blog')}
            className="flex items-center space-x-2"
          >
            <ArrowLeft className="h-4 w-4" />
            <span>목록으로</span>
          </Button>
        </div>

        <Card>
          <CardHeader>
            <div className="flex items-center space-x-4 text-sm text-muted-foreground mb-4">
              <div className="flex items-center space-x-1">
                <Calendar className="h-4 w-4" />
                <span>{formatDate(post.created_at)}</span>
              </div>
              <div className="flex items-center space-x-1">
                <User className="h-4 w-4" />
                <span>PHITSOL</span>
              </div>
            </div>
            <CardTitle className="text-2xl">{post.title}</CardTitle>
            <CardDescription>
              블로그 포스트 상세 내용
            </CardDescription>
          </CardHeader>
          <CardContent>
            {post.featured_image && (
              <div className="mb-6">
                <img
                  src={post.featured_image}
                  alt={post.title}
                  className="w-full h-64 object-cover rounded-lg"
                />
              </div>
            )}
            
            <div 
              className="prose prose-lg max-w-none"
              dangerouslySetInnerHTML={{ __html: post.content }}
            />
            
            <div className="mt-8 pt-6 border-t">
              <div className="flex items-center justify-between text-sm text-muted-foreground">
                <span>마지막 업데이트: {formatDate(post.updated_at)}</span>
                <span>상태: {post.status === 'published' ? '게시됨' : '임시저장'}</span>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </Layout>
  );
}
